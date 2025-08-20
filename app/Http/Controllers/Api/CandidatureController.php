<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCandidatureRequest;
use App\Http\Requests\UpdateCandidatureRequest;
use App\Models\Candidature;
use App\Traits\ApiResponseHandler;
use Illuminate\Http\JsonResponse;

class CandidatureController extends Controller
{
    use ApiResponseHandler;

    /**
     * Recruteur : Voir toutes les candidatures pour ses offres
     */
    public function index(): JsonResponse
    {
        return $this->handleApiNoTransaction(function () {
            $user = auth()->user();

            // On récupère uniquement les candidatures des offres du recruteur
            return Candidature::whereHas('offre', function($q) use ($user) {
                $q->where('employeur_id', $user->id);
            })->with(['particulier', 'offre.skills'])->get();
        });
    }

    /**
     * Candidat : Voir ses candidatures
     */
    public function myCandidatures(): JsonResponse
    {
        return $this->handleApiNoTransaction(function () {
            $user = auth()->user();

            return Candidature::where('particulier_id', $user->id)
                ->with(['offre.skills'])
                ->get();
        });
    }

    /**
     * Candidat : Créer une candidature
     */
    public function store(StoreCandidatureRequest $request): JsonResponse
    {
        return $this->handleApi(function () use ($request) {
            $data = $request->validated();
            $data['particulier_id'] = auth()->id();

            // Traitement des fichiers
            if ($request->hasFile('cv_url')) {
                $data['cv_url'] = $request->file('cv_url')->store('cvs', 'public');
            }
            if ($request->hasFile('motivation_url')) {
                $data['motivation_url'] = $request->file('motivation_url')->store('motivations', 'public');
            }

            $candidature = Candidature::create($data);

            return $candidature->load(['offre.skills']);
        }, 201);
    }

    /**
     * Candidat : Mettre à jour CV, lettre ou texte motivation
     */
    public function update(UpdateCandidatureRequest $request, Candidature $candidature): JsonResponse
    {
        return $this->handleApi(function () use ($request, $candidature) {
            $this->authorize('update', $candidature);

            $data = $request->validated();

            if ($request->hasFile('cv_url')) {
                $data['cv_url'] = $request->file('cv_url')->store('cvs', 'public');
            }
            if ($request->hasFile('motivation_url')) {
                $data['motivation_url'] = $request->file('motivation_url')->store('motivations', 'public');
            }

            $candidature->update($data);

            return $candidature->load(['offre.skills']);
        });
    }

    /**
     * Recruteur : Mettre à jour le statut ou la note IA
     */
    public function updateStatus(UpdateCandidatureRequest $request, Candidature $candidature): JsonResponse
    {
        return $this->handleApi(function () use ($request, $candidature) {
            $this->authorize('updateStatus', $candidature); // Politique: seule l'offre du recruteur

            $data = $request->validate();

            $candidature->update($data->only('statut', 'note_ia'));
            return $candidature->load(['particulier', 'offre.skills']);
        });
    }

    /**
     * Recruteur : Envoyer invitation entretien ou contrat
     */
    public function sendInvitation(Request $request, Candidature $candidature): JsonResponse
    {
        return $this->handleApi(function () use ($request, $candidature) {
            $this->authorize('sendInvitation', $candidature); // Politique

            // Ici tu peux gérer envoi email ou notification
            // Exemple : dispatch(new SendInvitationEmail($candidature));

            return ['message' => 'Invitation envoyée avec succès'];
        });
    }

}
