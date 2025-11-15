<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Enums\NiveauExp;
use App\Http\Enums\RoleValues;
use App\Http\Requests\StoreOffreEmploiRequest;
use App\Http\Requests\UpdateOffreEmploiRequest;
use App\Http\Resources\OffreResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\OffreEmploi;
use App\Models\OffreCategorie;
use App\Traits\ApiResponseHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class OffreEmploiController extends Controller
{
    use ApiResponseHandler, AuthorizesRequests;

    /**
     * Liste toutes les offres pour les candidats et marque si deja candidaté ou en favoris pour chaque candidat (public)
     */
    public function index(): JsonResponse
    {
        return $this->handleApiNoTransaction(function () {
            $user = auth()->user();
            $particulier = $user->particulier;

            // Récupérer tous les IDs des offres favorites et déjà candidaturées
            $favorisIds = $user->favoris()->pluck('offre_emploi_id')->toArray();
            $candidaturesIds = $particulier->candidatures()->pluck('offre_id')->toArray();

            // Récupérer les offres actives avec relations
            $offres = OffreEmploi::with([
                            'skills' => fn($q) => $q->orderBy('pivot_ordre_aff'),
                            'employeur',
                            'categorie'
                        ])
                        ->where('statut', 'active')
                        ->orderBy('id', 'desc')
                        ->get();

            // Ajouter les flags directement
            $offres->transform(function ($offre) use ($favorisIds, $candidaturesIds) {
                $offre->is_favoris = in_array($offre->id, $favorisIds);
                $offre->deja_candidature = in_array($offre->id, $candidaturesIds);
                return $offre;
            });

            return OffreResource::collection($offres);
            // $userId = auth()->id();

            // $offres = OffreEmploi::with(['skills' => fn($q) => $q->orderBy('pivot_ordre_aff'), 'employeur', 'categorie'])
            //                   ->where('statut', 'active')
            //                   ->get();

            // $offres = $offres->map(function ($offre) use ($userId) {
            //     $offre->is_favoris = $offre->favoris()->where('user_id', $userId)->exists();
            //     $offre->deja_candidature = $offre->candidatures()->where('user_id', $userId)->exists();
            //     return $offre;
            // });

            // return $offres;
        });
    }

    /**
     * Affiche une offre spécifique
     */
    public function show(OffreEmploi $offreEmploi): JsonResponse
    {
        // Incrémenter le compteur de vues
        $offreEmploi->increment('nombre_vues');
        
        return $this->handleApiNoTransaction(fn() =>
            new OffreResource(
                $offreEmploi
                    ->loadCount('candidatures')
                    ->load([
                        'skills' => fn($q) => $q->orderBy('pivot_ordre_aff'),
                        'employeur',
                        'categorie',
                    ])
            )
        );
    }

    /**
     * Crée une nouvelle offre (seulement pour le recruteur connecté)
     */
    public function store(StoreOffreEmploiRequest $request): JsonResponse
    {
        return $this->handleApi(function () use ($request) {
            $data = $request->validated();

            // Récupère automatiquement l'employeur depuis l'utilisateur connecté
            $data['employeur_id'] = $request->user()->professionnel->id;

            if ($request->user()->role_actif !== RoleValues::RECRUTEUR) {
                $request->user()->update(['role_actif' => RoleValues::RECRUTEUR]);
            }

            // Gestion des skills
            $skills = $data['skills'] ?? null;
            unset($data['skills']);

            // Gestion de l'upload du document d'annonce
            if ($request->hasFile('document_annonce')) {
                $data['document_annonce'] = $request->file('document_annonce')->store(
                    'documents_annonces', // dossier dans storage/app
                    'public' // disque "public"
                );
            }

            // Conversion des documents requis en JSON
            if (isset($data['documents_requis']) && is_array($data['documents_requis'])) {
                $data['documents_requis'] = json_encode($data['documents_requis']);
            }

            // Création de l'offre
            $offre = OffreEmploi::create($data);

            // Synchronisation des skills si présents
            if ($skills) {
                $syncData = [];
                foreach ($skills as $index => $skillId) {
                    $syncData[$skillId] = ['ordre_aff' => $index + 1];
                }
                $offre->skills()->sync($syncData);
            }

            // Retour avec les skills ordonnés
            return new OffreResource($offre->load([
                    'skills' => fn($q) => $q->orderBy('pivot_ordre_aff'),
                    'categorie',
                ])
            );
        }, 201);
    }

    /**
     * Met à jour une offre (seulement pour le recruteur propriétaire)
     */
    public function update(UpdateOffreEmploiRequest $request, OffreEmploi $offreEmploi): JsonResponse
    {
        return $this->handleApi(function () use ($request, $offreEmploi) {
            // Vérifie que l'offre appartient bien au recruteur
            $this->authorize('update', $offreEmploi);

            $data = $request->validated();

            // Skills à traiter séparément
            $skills = $data['skills'] ?? null;
            unset($data['skills']);

            // Gestion de l'upload du document d'annonce
            if ($request->hasFile('document_annonce')) {
                // Supprime l'ancien fichier si existe
                if ($offreEmploi->document_annonce && Storage::disk('public')->exists($offreEmploi->document_annonce)) {
                    Storage::disk('public')->delete($offreEmploi->document_annonce);
                }

                $data['document_annonce'] = $request->file('document_annonce')->store(
                    'documents_annonces',
                    'public'
                );
            }

            // Conversion des documents requis en JSON
            if (isset($data['documents_requis']) && is_array($data['documents_requis'])) {
                $data['documents_requis'] = json_encode($data['documents_requis']);
            }

            // Mise à jour de l'offre
            $offreEmploi->update($data);

            // Mise à jour des skills
            if ($skills !== null) {
                $syncData = [];
                foreach ($skills as $index => $skillId) {
                    $syncData[$skillId] = ['ordre_aff' => $index + 1];
                }
                $offreEmploi->skills()->sync($syncData);
            }

            return new OffreResource($offreEmploi->load([
                    'skills' => fn($q) => $q->orderBy('pivot_ordre_aff'),
                    'categorie',
                ])
            );
        });
    }

    /**
     * Supprime une offre (seulement pour le recruteur propriétaire)
     */
    public function destroy(OffreEmploi $offreEmploi): JsonResponse
    {
        return $this->handleApi(function () use ($offreEmploi) {
            $this->authorize('delete', $offreEmploi);

            $offreEmploi->delete();
            return null;
        }, 204);
    }

    /**
     * Liste les offres du recruteur connecté
     */
    public function mesOffres(): JsonResponse
    {
        return $this->handleApiNoTransaction(function () {
            $userId = auth()->id();
            return OffreResource::collection(
                OffreEmploi::with([
                  'skills' => fn($q) => $q->orderBy('pivot_ordre_aff'),
                  'categorie'
                ])  ->whereHas('employeur', fn($q) => $q->where('user_id', $userId))
                    ->get()
                    // ->paginate(10)
            );
        });
    }

    public function listNiveauExperience(): JsonResponse
    {
        return response()->json([
            'junior' => NiveauExp::JUNIOR->value,
            'intermediaire' => NiveauExp::INTERMEDIAIRE->value,
            'senior' => NiveauExp::SENIOR->value,
            'expert' => NiveauExp::EXPERT->value,
        ]);
    }
    /**
     * Catégories d'offres populaires (celles avec le plus de candidatures trie par ordre descroissant)
     */
    public function categoriesPopulaires(): JsonResponse
    {
        return $this->handleApiNoTransaction(function () {
            $categories = OffreCategorie::select('offre_categories.id', 'offre_categories.nom')
                ->join('offre_emplois', 'offre_emplois.categorie_id', '=', 'offre_categories.id')
                ->join('candidatures', 'candidatures.offre_id', '=', 'offre_emplois.id')
                ->where('offre_emplois.statut', 'active')
                ->groupBy('offre_categories.id', 'offre_categories.nom')
                ->selectRaw('COUNT(candidatures.id) as total_candidatures')
                ->orderByDesc('total_candidatures')
                ->get();

            return $categories;
        });
    }

}
