<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompetenceResource;
use App\Http\Resources\FreelancerResource;
use App\Http\Resources\RealisationResource;
use App\Models\Competences;
use App\Models\Freelancer;
use App\Models\FrelancerCompetences;
use App\Models\Realisation;
use App\Models\RealisationImage;
use App\Models\RealisationMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class FreelancerController extends Controller
{
    /**
     * Lister tous les freelancers (public)
     */
    public function index(){
        // $freelancers = Freelancer::with(['user', 'realisations.medias', 'notes'])->get();
        $freelancers = Freelancer::with(['user', 'notes'])->get();

        return response()->json([
            'data' => FreelancerResource::collection($freelancers),
        ]);
    }
    /**
     * Créer ou mettre à jour le profil freelancer
     */
    public function storeOrUpdate(Request $request)
    {
        $user = Auth::user();
        $freelancer = $user->freelancer;
        
        $request->validate([
            'nom_complet' => 'required|string|max:255',
            'titre_pro' => 'required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'competences' => 'sometimes|nullable|array',
            'competences.*' => 'required|string',
            'email_pro' => 'required|email|unique:freelancers,email_pro,' . ($freelancer ? $freelancer->id : 'NULL') . ',id',
            'telephone' => 'sometimes|nullable|string|max:20',
            'adresse' => 'sometimes|nullable|string|max:255',
            'ville' => 'sometimes|nullable|string|max:100',
            'pays' => 'sometimes|nullable|string|max:100',
            'photo_profil' => 'sometimes|nullable|image|max:2048',
            'photo_couverture' => 'sometimes|nullable|image|max:2048',
        ]);

        $data = $request->only([
            'nom_complet', 'titre_pro', 'description', 'email_pro', 'telephone', 
            'adresse', 'ville', 'pays',
        ]);
        $competences = $request->input('competences', []);
        
        
        if (isset($competences) && is_array($competences)) {
            $competences = array_map(function($competence) {
                $competenceName = ucwords(strtolower(trim($competence)));
                $competence = Competences::firstOrCreate(['nom' => $competenceName]);
                return $competence->id;
            }, $competences);
        }

        // Gérer les photos
        if ($request->hasFile('photo_profil')) {
            $data['photo_profil'] = $request->file('photo_profil')->store('freelancers/profils', 'public');
        }
        if ($request->hasFile('photo_couverture')) {
            $data['photo_couverture'] = $request->file('photo_couverture')->store('freelancers/couvertures', 'public');
        }

        // Si le freelancer existe déjà, mettre à jour, sinon créer
        if ($freelancer) {
            $freelancer->update($data);
        } else {
            $data['user_id'] = $user->id;
            $freelancer = Freelancer::create($data);
        }

        // Associer les competences
        $freelancer->competences()->sync($competences);

        return response()->json([
            'message' => 'Profil freelancer ' . ($freelancer->wasRecentlyCreated ? 'créé' : 'mis à jour') . ' avec succès',
            'data' => new FreelancerResource($freelancer->load('user', 'competences', 'realisations.medias', 'notes')),
        ], $freelancer->wasRecentlyCreated ? 201 : 200);
    }

    /**
     * Voir son propre profil freelancer
     */
    public function myProfile()
    {
        $user = Auth::user();
        $freelancer = $user->freelancer;
        
        if (!$freelancer) {
            return response()->json(['message' => 'Profil freelancer non trouvé'], 404);
        }

        return response()->json([
            'data' => new FreelancerResource($freelancer->load('user', 'competences', 'realisations.medias', 'notes', 'missions')),
        ]);
    }

    /**
     * Voir le profil d'un freelancer (public)
     */
    public function show($id)
    {
        $freelancer = Freelancer::with(['user', 'competences', 'realisations.medias', 'notes', 'missions'])
            ->findOrFail($id);

        return response()->json([
            'data' => new FreelancerResource($freelancer),
        ]);
    }

    /**
     * Créer une réalisation
     */
    public function storeRealisation(Request $request)
    {
        $user = Auth::user();
        $freelancer = $user->freelancer;

        if (!$freelancer) {
            return response()->json(['message' => 'Profil freelancer non trouvé'], 404);
        }

        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'date_realisation' => 'required|date',
            'localisation' => 'sometimes|nullable|string|max:255',
            'lien' => 'sometimes|nullable|url',
            'images.*' => 'nullable|image|max:10240', // max 10MB par image
            'documents.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt|max:10240', // max 10MB par document
            'videos.*' => 'nullable|file|mimes:mp4,avi,mov,wmv,flv,webm|max:102400', // max 100MB par vidéo
        ]);

        DB::beginTransaction();
        try {
            $realisation = Realisation::create([
                'freelancer_id' => $freelancer->id,
                'titre' => $request->titre,
                'description' => $request->description,
                'date_realisation' => $request->date_realisation,
                'localisation' => $request->localisation,
                'lien' => $request->lien,
            ]);

            // Upload des images
            if ($request->hasFile('images')) {
                $images = $request->file('images');
                if (!is_array($images)) {
                    $images = [$images];
                }

                foreach ($images as $image) {
                    $path = $image->store('realisations/images', 'public');
                    RealisationMedia::create([
                        'realisation_id' => $realisation->id,
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
                    $path = $document->store('realisations/documents', 'public');
                    RealisationMedia::create([
                        'realisation_id' => $realisation->id,
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
                    $path = $video->store('realisations/videos', 'public');
                    RealisationMedia::create([
                        'realisation_id' => $realisation->id,
                        'media_type' => 'video',
                        'media_path' => $path,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Réalisation créée avec succès',
                'data' => new RealisationResource($realisation->load('medias')),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erreur lors de la création de la réalisation', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Mettre à jour une réalisation
     */
    public function updateRealisation(Request $request, $realisationId)
    {
        $user = Auth::user();
        $freelancer = $user->freelancer;

        if (!$freelancer) {
            return response()->json(['message' => 'Profil freelancer non trouvé'], 404);
        }

        $realisation = Realisation::where('freelancer_id', $freelancer->id)
            ->findOrFail($realisationId);

        $request->validate([
            'titre' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'date_realisation' => 'sometimes|required|date',
            'localisation' => 'sometimes|string|max:255',
            'lien' => 'sometimes|url',
            // 'images.*' => 'sometimes|image|max:2048',
            'medias_to_delete' => 'nullable|array',
            'medias_to_delete.*' => 'exists:realisation_medias,id',
        ]);

        DB::beginTransaction();
        try {
            $realisation->update($request->only([
                'titre', 'description', 'date_realisation', 'localisation', 'lien'
            ]));

            // Supprimer les images sélectionnées
            if ($request->has('medias_to_delete')) {
                $mediasToDelete = RealisationMedia::whereIn('id', $request->medias_to_delete)
                    ->where('realisation_id', $realisation->id)
                    ->get();
                
                foreach ($mediasToDelete as $media) {
                    if ($media->media_path && Storage::disk('public')->exists($media->media_path)) {
                        Storage::disk('public')->delete($media->media_path);
                    }
                    $media->delete();
                }
            }

            // Ajouter de nouvelles images
            // if ($request->hasFile('images')) {
            //     foreach ($request->file('images') as $image) {
            //         $path = $image->store('realisations', 'public');
            //         RealisationImage::create([
            //             'realisation_id' => $realisation->id,
            //             'image' => $path,
            //         ]);
            //     }
            // }

            DB::commit();

            return response()->json([
                'message' => 'Réalisation mise à jour avec succès',
                'data' => new RealisationResource($realisation->load('images')),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erreur lors de la mise à jour', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Supprimer une réalisation
     */
    public function destroyRealisation($realisationId)
    {
        $user = Auth::user();
        $freelancer = $user->freelancer;

        if (!$freelancer) {
            return response()->json(['message' => 'Profil freelancer non trouvé'], 404);
        }

        $realisation = Realisation::where('freelancer_id', $freelancer->id)
            ->findOrFail($realisationId);

        // Supprimer les images associées
        foreach ($realisation->medias as $media) {
            if ($media->media_path && Storage::disk('public')->exists($media->media_path)) {
                Storage::disk('public')->delete($media->media_path);
            }
        }

        $realisation->delete();

        return response()->json(['message' => 'Réalisation supprimée avec succès']);
    }

    /**
     * Lister toutes les réalisations d'un freelancer
     */
    public function realisations($freelancerId = null)
    {
        $freelancerId = $freelancerId ?? Auth::user()->freelancer->id ?? null;
        
        if (!$freelancerId) {
            return response()->json(['message' => 'Freelancer non trouvé'], 404);
        }

        $freelancer = Freelancer::findOrFail($freelancerId);
        $realisations = $freelancer->realisations()->with('medias')->get();

        return response()->json([
            'data' => RealisationResource::collection($realisations),
        ]);
    }
    
    /**
     * Ajouter des médias à une réalisation existante
     */
    public function addRealisationMedia(Request $request, $realisationId)
    {
        $user = Auth::user();
        $realisation = Realisation::findOrFail($realisationId);

        // Vérifier que l'utilisateur est le freelancer de cette réalisation
        if ($realisation->freelancer_id !== $user->freelancer->id) {
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
                $path = $image->store('realisations/images', 'public');
                RealisationMedia::create([
                    'realisation_id' => $realisation->id,
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
                $path = $document->store('realisations/documents', 'public');
                RealisationMedia::create([
                    'realisation_id' => $realisation->id,
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
                $path = $video->store('realisations/videos', 'public');
                RealisationMedia::create([
                    'realisation_id' => $realisation->id,
                    'media_type' => 'video',
                    'media_path' => $path,
                ]);
            }
        }

        return response()->json([
            'message' => 'Médias ajoutés avec succès',
            'data' => new RealisationResource($realisation->load('medias')),
        ]);
    }

    /**
     * Supprimer un média d'une mission
     */
    public function deleteRealisationMedia($realisationId, $mediaId)
    {
        $user = Auth::user();
        $realisation = Realisation::findOrFail($realisationId);

        // Vérifier que l'utilisateur est le freelancer de cette réalisation
        if ($realisation->freelancer_id !== $user->freelancer->id) {
            return response()->json(['error' => 'Accès refusé'], 403);
        }

        $media = RealisationMedia::where('realisation_id', $realisation->id)
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
     * Ajouter une compétence
     */
    public function addCompetence(Request $request)
    {
        // Normalement autorisé uniquement pour les admins

        $data = $request->validate([
            'nom' => 'required|string|unique:competences,nom',
            'description' => 'sometimes|nullable|string',
        ]);

        $data['nom'] = ucwords(strtolower(trim($data['nom'])));
        try {
            Competences::create($data);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => 'Cette compétence existe déjà.'], 409);
        }

        return response()->json([
            'message' => 'Compétence ajoutée avec succès',
            'data' => Competences::where('nom', $data['nom'])->first(),
        ]);
    }

    /**
     * Lister toutes les compétences
     */
    public function listCompetences()
    {
        return response()->json([
            'data' => Competences::all(),
        ]);
    }

    /**
     * Supprimer une compétence
     */
    public function removeCompetence($competenceId)
    {
        // Normalement autorisé uniquement pour les admins

        Competences::findOrFail($competenceId)->delete();

        return response()->json([
            'message' => 'Compétence supprimée avec succès',
        ]);
    }

    /**
     * Lister les compétences du freelancer connecté
     */
    public function listFreelancerCompetence()
    {
        $user = Auth::user();
        $freelancer = $user->freelancer;

        if (!$freelancer) {
            return response()->json(['message' => 'Profil freelancer non rencontré'], 404);
        }

        $competences = $freelancer->competences;

        return response()->json([
            'data' => CompetenceResource::collection($competences),
        ]);
    }

    /**
     * Ajouter une compétence au freelancer connecté
     */
    public function addFreelancerCompetenceOriginal(Request $request)
    {
        $user = Auth::user();
        $freelancer = $user->freelancer;

        if (!$freelancer) {
            return response()->json(['message' => 'Profil freelancer non rencontré'], 404);
        }

        $data = $request->validate([
            // 'competence_id' => 'required|exists:competences,id',
            'nom' => 'required|string|max:100',
            'niveau' => 'sometimes|nullable|string|max:100',
        ]);

        $data['nom'] = ucwords(strtolower(trim($data['nom'])));
        
        $competence = Competences::firstOrCreate([
            'nom' => $data['nom'],
            'description' => $data['description'] ?? '',
        ]);

        $data = [
            'freelancer_id' => $freelancer->id,
            'competence_id' => $competence->id,
            'niveau' => $data['niveau'] ?? null,
        ];
        
        // $freelancer->competences()->updateOrCreate(
        FrelancerCompetences::updateOrCreate(
            ['freelancer_id' => $freelancer->id, 'competence_id' => $competence->id],
            $data
        );

        return response()->json([
            'message' => 'Compétence ajoutée avec succès',
        ]); 
    }

    /**
     * Ajouter une compétence au freelancer connecté
     */
    public function addFreelancerCompetence(Request $request)
    {
        $user = Auth::user();
        $freelancer = $user->freelancer;

        if (!$freelancer) {
            return response()->json(['message' => 'Profil freelancer non rencontré'], 404);
        }
        
        $request->validate([
            'competences' => 'sometimes|nullable|array',
            'competences.*' => 'required|string',
        ]);

        $competences = $request->input('competences', []);
                
        if (isset($competences) && is_array($competences)) {
            $competences = array_map(function($competence) {
                $competenceName = ucwords(strtolower(trim($competence)));
                $competence = Competences::firstOrCreate(['nom' => $competenceName]);
                return $competence->id;
            }, $competences);
        }
        
        // Associer les competences
        $freelancer->competences()->sync($competences);

        return response()->json([
            'message' => 'Compétence ajoutée avec succès',
        ]); 
    }

    /**
     * Supprimer une compétence au freelancer connecté
     */
    public function removeFreelancerCompetence($competenceId)
    {
        $user = Auth::user();
        $freelancer = $user->freelancer;

        if (!$freelancer) {
            return response()->json(['message' => 'Profil freelancer non rencontré'], 404);
        }
        
        $freelancer->competences()->detach($competenceId);

        return response()->json([
            'message' => 'Compétence supprimée avec succès',
        ]);
    }
}

