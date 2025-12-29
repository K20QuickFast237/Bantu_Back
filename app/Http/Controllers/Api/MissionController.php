<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MissionResource;
use App\Http\Resources\FreelancerNoteResource;
use App\Models\Mission;
use App\Models\MissionMedia;
use App\Models\Freelancer;
use App\Models\FreelancerNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MissionController extends Controller
{
    /**
     * Créer une mission (client → freelancer)
     */
    public function store(Request $request)
    {
        $user = Auth::user(); // Le client

        // Forcer le traitement comme requête JSON pour éviter les redirections
        // $request->headers->set('Accept', 'application/json');

        $validated = $request->validate([
            'freelancer_id' => 'required|exists:freelancers,id',
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'date_debut' => 'required|date',
            'date_fin' => 'sometimes|nullable|date|after:date_debut',
            'cout' => 'sometimes|nullable|numeric|min:0',
            'images.*' => 'nullable|image|max:10240', // max 10MB par image
            'documents.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt|max:10240', // max 10MB par document
            'videos.*' => 'nullable|file|mimes:mp4,avi,mov,wmv,flv,webm|max:102400', // max 100MB par vidéo
        ]);

        DB::beginTransaction();
        try {
            $mission = Mission::create([
                'freelancer_id' => $request->freelancer_id,
                'client_id' => $user->id,
                'titre' => $request->titre,
                'description' => $request->description,
                'date_debut' => $request->date_debut,
                'date_fin' => $request->date_fin,
                'cout' => $request->cout,
            ]);

            // Upload des images
            if ($request->hasFile('images')) {
                $images = $request->file('images');
                if (!is_array($images)) {
                    $images = [$images];
                }

                foreach ($images as $image) {
                    $path = $image->store('missions/images', 'public');
                    MissionMedia::create([
                        'mission_id' => $mission->id,
                        'media_type' => 'image',
                        'media_path' => $path,
                    ]);
                }
            }

            // Upload des documents
            if ($request->hasFile('documents')) {
                $documents = $request->file('documents');
                if (!is_array($documents)) {
                    $documents = [$documents];
                }

                foreach ($documents as $document) {
                    $path = $document->store('missions/documents', 'public');
                    MissionMedia::create([
                        'mission_id' => $mission->id,
                        'media_type' => 'document',
                        'media_path' => $path,
                    ]);
                }
            }

            // Upload des vidéos
            if ($request->hasFile('videos')) {
                $videos = $request->file('videos');
                if (!is_array($videos)) {
                    $videos = [$videos];
                }

                foreach ($videos as $video) {
                    $path = $video->store('missions/videos', 'public');
                    MissionMedia::create([
                        'mission_id' => $mission->id,
                        'media_type' => 'video',
                        'media_path' => $path,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Mission créée avec succès',
                'data' => new MissionResource($mission->load('freelancer.user', 'client', 'medias')),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Erreur lors de la création de la mission',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lister les missions d'un client
     */
    public function myMissions()
    {
        $user = Auth::user();
        $missions = Mission::where('client_id', $user->id)
            ->with(['freelancer.user', 'medias'])
            ->latest()
            ->get();

        return response()->json([
            'data' => MissionResource::collection($missions),
        ]);
    }

    /**
     * Lister les missions d'un freelancer
     */
    public function freelancerMissions()
    {
        $user = Auth::user();
        $freelancer = $user->freelancer;

        if (!$freelancer) {
            return response()->json(['message' => 'Profil freelancer non trouvé'], 404);
        }

        $missions = Mission::where('freelancer_id', $freelancer->id)
            ->with(['client', 'medias'])
            ->latest()
            ->get();

        return response()->json([
            'data' => MissionResource::collection($missions),
        ]);
    }

    /**
     * Voir les détails d'une mission
     */
    public function show($id)
    {
        $user = Auth::user();
        $mission = Mission::with(['freelancer.user', 'client', 'medias'])
            ->findOrFail($id);

        // Vérifier que l'utilisateur est soit le client soit le freelancer
        $freelancer = $user->freelancer;
        if ($mission->client_id !== $user->id && (!$freelancer || $mission->freelancer_id !== $freelancer->id)) {
            return response()->json(['error' => 'Accès refusé'], 403);
        }

        return response()->json([
            'data' => new MissionResource($mission),
        ]);
    }

    /**
     * Laisser une note à un freelancer après la réalisation d'une mission
     */
    public function leaveNote(Request $request, $missionId)
    {
        $user = Auth::user(); // Le client
        try {
            $mission = Mission::findOrFail($missionId);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Mission non trouvée'], 404);
        }

        // Vérifier que l'utilisateur est le client de cette mission
        if ($mission->client_id !== $user->id) {
            return response()->json(['error' => 'Accès refusé'], 403);
        }

        // Vérifier qu'une note n'existe pas déjà pour cette mission
        // On peut permettre plusieurs notes mais on vérifie juste qu'il n'y a pas de doublon exact
        $existingNote = FreelancerNote::where('freelancer_id', $mission->freelancer_id)
            ->where('client_id', $user->id)
            ->first();

        if ($existingNote) {
            return response()->json(['error' => 'Une note a déjà été laissée pour ce freelancer'], 409);
        }

        $request->validate([
            'score' => 'required|integer|min:1|max:5',
            'description' => 'required|string|max:1000',
        ]);

        $note = FreelancerNote::create([
            'freelancer_id' => $mission->freelancer_id,
            'client_id' => $user->id,
            'mission_id' => $mission->id,
            'score' => $request->score,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Note laissée avec succès',
            'data' => new FreelancerNoteResource($note->load('freelancer', 'client')),
        ], 201);
    }

    /**
     * Mettre à jour une note (si nécessaire)
     */
    public function updateNote(Request $request, $noteId)
    {
        $user = Auth::user();
        $note = FreelancerNote::findOrFail($noteId);

        // Vérifier que l'utilisateur est le client qui a laissé la note
        if ($note->client_id !== $user->id) {
            return response()->json(['error' => 'Accès refusé'], 403);
        }

        $request->validate([
            'score' => 'sometimes|required|integer|min:1|max:5',
            'description' => 'sometimes|required|string|max:1000',
        ]);

        $note->update($request->only(['score', 'description']));

        return response()->json([
            'message' => 'Note mise à jour avec succès',
            'data' => new FreelancerNoteResource($note->load('freelancer', 'client')),
        ]);
    }

    /**
     * Ajouter des médias à une mission existante
     */
    public function addMedia(Request $request, $missionId)
    {
        $user = Auth::user();
        $mission = Mission::findOrFail($missionId);

        // Vérifier que l'utilisateur est le client de cette mission
        if ($mission->client_id !== $user->id) {
            return response()->json(['error' => 'Accès refusé'], 403);
        }

        // Forcer le traitement comme requête JSON pour éviter les redirections
        // $request->headers->set('Accept', 'application/json');

        try {
            $request->validate([
                'images.*' => 'nullable|image|max:10240', // max 10MB par image
                'documents.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt|max:10240', // max 10MB par document
                'videos.*' => 'nullable|file|mimes:mp4,avi,mov,wmv,flv,webm|max:102400', // max 100MB par vidéo
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        }

        if (!$request->hasFile('images') && !$request->hasFile('documents') && !$request->hasFile('videos')) {
            return response()->json(['error' => 'Aucun fichier fourni'], 400);
        }

        // Upload des images
        if ($request->hasFile('images')) {
            $images = $request->file('images');
            if (!is_array($images)) {
                $images = [$images];
            }

            foreach ($images as $image) {
                $path = $image->store('missions/images', 'public');
                MissionMedia::create([
                    'mission_id' => $mission->id,
                    'media_type' => 'image',
                    'media_path' => $path,
                ]);
            }
        }

        // Upload des documents
        if ($request->hasFile('documents')) {
            $documents = $request->file('documents');
            if (!is_array($documents)) {
                $documents = [$documents];
            }

            foreach ($documents as $document) {
                $path = $document->store('missions/documents', 'public');
                MissionMedia::create([
                    'mission_id' => $mission->id,
                    'media_type' => 'document',
                    'media_path' => $path,
                ]);
            }
        }

        // Upload des vidéos
        if ($request->hasFile('videos')) {
            $videos = $request->file('videos');
            if (!is_array($videos)) {
                $videos = [$videos];
            }

            foreach ($videos as $video) {
                $path = $video->store('missions/videos', 'public');
                MissionMedia::create([
                    'mission_id' => $mission->id,
                    'media_type' => 'video',
                    'media_path' => $path,
                ]);
            }
        }

        return response()->json([
            'message' => 'Médias ajoutés avec succès',
            'data' => new MissionResource($mission->load('medias')),
        ]);
    }

    /**
     * Supprimer un média d'une mission
     */
    public function deleteMedia($missionId, $mediaId)
    {
        $user = Auth::user();
        $mission = Mission::findOrFail($missionId);

        // Vérifier que l'utilisateur est le client de cette mission
        if ($mission->client_id !== $user->id) {
            return response()->json(['error' => 'Accès refusé'], 403);
        }

        $media = MissionMedia::where('mission_id', $mission->id)
            ->findOrFail($mediaId);

        // Supprimer le fichier du storage
        if ($media->media_path && Storage::disk('public')->exists($media->media_path)) {
            Storage::disk('public')->delete($media->media_path);
        }

        $media->delete();

        return response()->json([
            'message' => 'Média supprimé avec succès',
        ]);
    }

    /**
     * Mettre à jour le statut d'une mission (client ou freelancer)
     */
    public function updateStatus(Request $request, $missionId)
    {
        $user = Auth::user();
        $mission = Mission::findOrFail($missionId);

        // Vérifier que l'utilisateur est soit le client soit le freelancer
        $freelancer = $user->freelancer;
        $isClient = $mission->client_id === $user->id;
        $isFreelancer = $freelancer && $mission->freelancer_id === $freelancer->id;

        if (!$isClient && !$isFreelancer) {
            return response()->json(['error' => 'Accès refusé'], 403);
        }

        // Définir les statuts possibles
        $statutsPossibles = [
            'En Attente',
            'Acceptée',
            'En Cours',
            'Terminée',
            'Annulée',
            'En Pause'
        ];

        $request->validate([
            'statut' => 'required|string|in:' . implode(',', $statutsPossibles),
        ]);

        // Règles de transition de statut selon le rôle
        $statutActuel = $mission->statut;
        $nouveauStatut = $request->statut;

        // Si le statut est déjà le même, retourner sans modification
        if ($statutActuel === $nouveauStatut) {
            return response()->json([
                'message' => 'Le statut est déjà "' . $nouveauStatut . '"',
                'data' => new MissionResource($mission->load('freelancer.user', 'client', 'medias')),
            ]);
        }

        // Transitions autorisées pour le CLIENT
        $transitionsClient = [
            'En Attente' => ['Annulée'],
            'Acceptée' => ['Annulée'],
            'En Cours' => ['Annulée'],
            'En Pause' => ['Annulée'],
        ];

        // Transitions autorisées pour le FREELANCER
        $transitionsFreelancer = [
            'En Attente' => ['Acceptée', 'Annulée'],
            'Acceptée' => ['En Cours'],
            'En Cours' => ['Terminée', 'En Pause'],
            'En Pause' => ['En Cours', 'Terminée'],
        ];

        $transitionAutorisee = false;

        if ($isClient) {
            // Le client peut annuler à tout moment (sauf si déjà terminée ou annulée)
            if ($nouveauStatut === 'Annulée' && $statutActuel !== 'Terminée' && $statutActuel !== 'Annulée') {
                $transitionAutorisee = true;
            }
            // Vérifier les autres transitions autorisées pour le client
            elseif (isset($transitionsClient[$statutActuel]) && in_array($nouveauStatut, $transitionsClient[$statutActuel])) {
                $transitionAutorisee = true;
            }
        } 
        if ($isFreelancer) {
            // Vérifier les transitions autorisées pour le freelancer
            if (isset($transitionsFreelancer[$statutActuel]) && in_array($nouveauStatut, $transitionsFreelancer[$statutActuel])) {
                $transitionAutorisee = true;
            }
        }

        if (!$transitionAutorisee) {
            return response()->json([
                'error' => 'Transition de statut non autorisée',
                'statut_actuel' => $statutActuel,
                'statut_demande' => $nouveauStatut,
                'role' => $isClient ? 'client' : 'freelancer'
            ], 403);
        }

        // Mettre à jour le statut
        $mission->update(['statut' => $nouveauStatut]);

        return response()->json([
            'message' => 'Statut de la mission mis à jour avec succès',
            'data' => new MissionResource($mission->load('freelancer.user', 'client', 'medias')),
        ]);
    }
}

