<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = [
            'id' => $this->id,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'email' => $this->email,
            'role' => $this->role,
            'actif' => (bool)$this->is_active,
            'last_login' => $this->last_login,
            'email_verified_at' => $this->email_verified_at,
        ];
        if ($status =$this->isCandidat() || $this->isRecruteur()) {
            $user['profilCompleted'] = $status;
        }
        return $user;
        // [
        //     'id' => $this->id,
        //     'nom' => $this->nom,
        //     'prenom' => $this->prenom,
        //     'email' => $this->email,
        //     'role' => $this->role,
        //     'actif' => (bool)$this->is_active,
        //     'last_login' => $this->last_login,
        //     'email_verified_at' => $this->email_verified_at,
        // ];
    }
}
