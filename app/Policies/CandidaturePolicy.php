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
     * Determine whether the user can view the model.
     */
    public function view(User $user, Candidature $candidature): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Vérifie si l'utilisateur peut mettre à jour sa candidature
     */
    public function update(User $user, Candidature $candidature)
    {
        return $user->id === $candidature->particulier_id;
    }

    /**
     * Vérifie si le recruteur peut mettre à jour le statut
     */
    public function updateStatus(User $user, Candidature $candidature)
    {
        return $user->id === $candidature->offre->employeur_id;
    }

    /**
     * Vérifie si le recruteur peut envoyer une invitation
     */
    public function sendInvitation(User $user, Candidature $candidature)
    {
        return $user->id === $candidature->offre->employeur_id;
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
}
