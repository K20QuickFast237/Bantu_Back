<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCandidatureRequest;
use App\Http\Requests\UpdateCandidatureRequest;
use App\Http\Requests\UpdateCandidatureRecruteurRequest;
use App\Models\Candidature;
use App\Traits\ApiResponseHandler;
use Illuminate\Http\JsonResponse;
use App\Services\CvSnapshotService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CandidatureController extends Controller
{
    use ApiResponseHandler, AuthorizesRequests;

    /**
     * Recruteur : Voir toutes les candidatures pour ses offres
     */
    public function index(): JsonResponse
    {
        return $this->handleApiNoTransaction(function () {
            $user = auth()->user();
            $professionnel = $user->professionnel;

            return Candidature::whereHas('offre', function ($q) use ($user) {
                $q->where('employeur_id', $user->professionnel->id);
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
            $particulier = $user->particulier;

            return Candidature::where('particulier_id', $particulier->id)
                ->with(['offre.skills'])
                ->get();
        });
    }

    /**
     * Candidat : Créer une candidature
     */
    public function store(StoreCandidatureRequest $request, CvSnapshotService $cvSnapshotService): JsonResponse
    {
        return $this->handleApi(function () use ($request, $cvSnapshotService) {
            $data = $request->validated();
            $user = auth()->user();
            $particulier = $user->particulier;
            $data['particulier_id'] = $particulier->id;

            // Upload fichiers
            if ($request->hasFile('cv_url')) {
                $data['cv_url'] = $request->file('cv_url')->store('cvs', 'public');
            }
            if ($request->hasFile('motivation_url')) {
                $data['motivation_url'] = $request->file('motivation_url')->store('motivations', 'public');
            }

            // Snapshot JSON du profil si candidature avec profil
            if ($request->boolean('cv_genere', false)) {
                $data['cv_genere'] = $cvSnapshotService->generate(auth()->user());
            }

            $candidature = Candidature::create($data);

            return $candidature->load(['offre.skills']);
        }, 201);
    }

    /**
     * Candidat : Modifier sa candidature
     * Seulement si statut = en_revision (policy)
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
     * Recruteur : Mettre à jour statut ou note IA
     */
    public function updateStatus(UpdateCandidatureRecruteurRequest $request, Candidature $candidature): JsonResponse
    {
        return $this->handleApi(function () use ($request, $candidature) {
            $this->authorize('updateStatus', $candidature);

            $data = $request->validated();

            $candidature->update([
                'statut' => $data['statut'] ?? $candidature->statut,
                'note_ia' => $data['note_ia'] ?? $candidature->note_ia,
            ]);

            return $candidature->load(['particulier', 'offre.skills']);
        });
    }

}
