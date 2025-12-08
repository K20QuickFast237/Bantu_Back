<?php

namespace App\Policies;

use App\Models\Panier;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PanierPolicy
{
        /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Panier $panier): bool
    {
        return $user->id === $panier->acheteur->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Panier $panier): bool
    {
        return $user->id === $panier->acheteur->user_id;
    }
}
