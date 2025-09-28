<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOffreEmploiRequest;
use App\Http\Requests\UpdateOffreEmploiRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\OffreEmploi;
use App\Traits\ApiResponseHandler;
use Illuminate\Http\JsonResponse;

class OffreEmploiController extends Controller
{
    use ApiResponseHandler, AuthorizesRequests;

    /**
     * Liste toutes les offres pour les candidats (public)
     */
    public function index(): JsonResponse
    {
        return $this->handleApiNoTransaction(function () {
            return OffreEmploi::with(['skills' => fn($q) => $q->orderBy('pivot_ordre_aff'), 'employeur'])
                              ->where('statut', 'active')
                              ->paginate(10);
        });
    }

    /**
     * Affiche une offre spécifique
     */
    public function show(OffreEmploi $offreEmploi): JsonResponse
    {
        return $this->handleApiNoTransaction(fn() => 
            $offreEmploi->load(['skills' => fn($q) => $q->orderBy('pivot_ordre_aff'), 'employeur'])
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
            return $offre->load(['skills' => fn($q) => $q->orderBy('pivot_ordre_aff')]);
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
                if ($offreEmploi->document_annonce && \Storage::disk('public')->exists($offreEmploi->document_annonce)) {
                    \Storage::disk('public')->delete($offreEmploi->document_annonce);
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

            return $offreEmploi->load(['skills' => fn($q) => $q->orderBy('pivot_ordre_aff')]);
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
            return OffreEmploi::with(['skills' => fn($q) => $q->orderBy('pivot_ordre_aff')])
                              ->whereHas('employeur', fn($q) => $q->where('user_id', $userId))
                              ->paginate(10);
        });
    }
}
