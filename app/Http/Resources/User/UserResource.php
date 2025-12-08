<?php
namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->roles->pluck('name'),
            'acheteur' => $this->acheteur ? new \App\Http\Resources\Acheteur\AcheteurResource($this->acheteur) : null,
            'vendeur' => $this->vendeur ? new \App\Http\Resources\Vendeur\VendeurResource($this->vendeur) : null,
        ];
    }
}