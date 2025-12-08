<?php

namespace App\Policies;

use App\Models\Favori;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FavoriPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Favori $favori): bool
    {
        return $user->id === $favori->acheteur->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Favori $favori): bool
    {
        return $user->id === $favori->acheteur->user_id;
    }
}
