<?php

namespace App\Policies;

use App\Models\Candidature;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CandidaturePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    
    /**
     * Un utilisateur peut voir une candidature :
     * - s’il est le candidat concerné
     * - OU s’il est le recruteur propriétaire de l’offre
     */
    public function view(User $user, Candidature $candidature): bool
    {
        $isCandidat = $user->particulier
            && $user->particulier->id === $candidature->particulier_id;

        $isRecruteur = $user->professionnel
            && $user->professionnel->id === $candidature->offre->employeur_id;

        return $isCandidat || $isRecruteur;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Candidature $candidature): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Candidature $candidature): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Candidature $candidature): bool
    {
        return false;
    }

    /**
     * Un particulier peut mettre à jour SA candidature
     * uniquement si elle est encore en "en_revision".
     */
    public function update(User $user, Candidature $candidature): bool
    {
        // Vérifie que le user a bien un profil particulier
        if (!$user->particulier) {
            return false;
        }

        return $user->particulier->id === $candidature->particulier_id
            && $candidature->statut === 'en_revision';
    }

    /**
     * Un recruteur peut changer le statut
     * uniquement si la candidature appartient à une de SES offres.
     */
    public function updateStatus(User $user, Candidature $candidature): bool
    {
        // Vérifie que le user a bien un profil pro
        if (!$user->professionnel) {
            return false;
        }

        return $user->professionnel->id === $candidature->offre->employeur_id;
    }

    /**
     * Un recruteur peut envoyer une invitation
     * uniquement si la candidature appartient à une de SES offres.
     */
    public function sendInvitation(User $user, Candidature $candidature): bool
    {
        return $user->id === $candidature->offre->employeur_id;
    }



}
