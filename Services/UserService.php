<?php
namespace App\Services;

use App\Models\User;

class UserService
{
    public function getUser($id)
    {
        return User::find($id);
    }
}