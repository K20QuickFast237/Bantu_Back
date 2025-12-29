<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FreelancerResource;
use App\Http\Resources\RealisationResource;
use App\Models\Freelancer;
use App\Models\Realisation;
use App\Models\RealisationImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class FreelancerController extends Controller
{
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
            'email_pro' => 'required|email|unique:freelancers,email_pro,' . ($freelancer ? $freelancer->id : 'NULL') . ',id',
            'telephone' => 'sometimes|nullable|string|max:20',
            'adresse' => 'sometimes|nullable|string|max:255',
            'ville' => 'sometimes|nullable|string|max:100',
            'pays' => 'sometimes|nullable|string|max:100',
            'photo_profil' => 'sometimes|nullable|image|max:2048',
            'photo_couverture' => 'sometimes|nullable|image|max:2048',
        ]);

        $data = $request->only([
            'nom_complet', 'titre_pro', 'email_pro', 'telephone', 
            'adresse', 'ville', 'pays'
        ]);

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

        return response()->json([
            'message' => 'Profil freelancer ' . ($freelancer->wasRecentlyCreated ? 'créé' : 'mis à jour') . ' avec succès',
            'data' => new FreelancerResource($freelancer->load('user', 'realisations.images', 'notes')),
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
            'data' => new FreelancerResource($freelancer->load('user', 'realisations.images', 'notes', 'missions')),
        ]);
    }

    /**
     * Voir le profil d'un freelancer (public)
     */
    public function show($id)
    {
        $freelancer = Freelancer::with(['user', 'realisations.images', 'notes'])
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
            'images.*' => 'nullable|image|max:2048',
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
                foreach ($request->file('images') as $image) {
                    $path = $image->store('realisations', 'public');
                    RealisationImage::create([
                        'realisation_id' => $realisation->id,
                        'image' => $path,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Réalisation créée avec succès',
                'data' => new RealisationResource($realisation->load('images')),
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
            'images.*' => 'sometimes|image|max:2048',
            'images_to_delete' => 'nullable|array',
            'images_to_delete.*' => 'exists:realisation_images,id',
        ]);

        DB::beginTransaction();
        try {
            $realisation->update($request->only([
                'titre', 'description', 'date_realisation', 'localisation', 'lien'
            ]));

            // Supprimer les images sélectionnées
            if ($request->has('images_to_delete')) {
                $imagesToDelete = RealisationImage::whereIn('id', $request->images_to_delete)
                    ->where('realisation_id', $realisation->id)
                    ->get();
                
                foreach ($imagesToDelete as $image) {
                    if ($image->image && Storage::disk('public')->exists($image->image)) {
                        Storage::disk('public')->delete($image->image);
                    }
                    $image->delete();
                }
            }

            // Ajouter de nouvelles images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('realisations', 'public');
                    RealisationImage::create([
                        'realisation_id' => $realisation->id,
                        'image' => $path,
                    ]);
                }
            }

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
        foreach ($realisation->images as $image) {
            if ($image->image && Storage::disk('public')->exists($image->image)) {
                Storage::disk('public')->delete($image->image);
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
        $realisations = $freelancer->realisations()->with('images')->get();

        return response()->json([
            'data' => RealisationResource::collection($realisations),
        ]);
    }
}

