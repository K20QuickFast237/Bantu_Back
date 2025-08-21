<?php

namespace App\Policies;

use App\Models\OffreEmploi;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OffreEmploiPolicy
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
    public function view(User $user, OffreEmploi $offreEmploi): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OffreEmploi $offreEmploi): bool
    {
        return $this->isProprietaire($user, $offreEmploi);;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OffreEmploi $offreEmploi): bool
    {
        return $this->isProprietaire($user, $offreEmploi);;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, OffreEmploi $offreEmploi): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, OffreEmploi $offreEmploi): bool
    {
        return false;
    }

    /**
     * Vérifie si l'utilisateur connecté est bien le recruteur propriétaire de l'offre
     */
    protected function isProprietaire(User $user, OffreEmploi $offreEmploi): bool
    {
        // On suppose que User -> Professionnel relation
        return $user->professionnel && $user->professionnel->id === $offreEmploi->employeur_id;
    }
}
