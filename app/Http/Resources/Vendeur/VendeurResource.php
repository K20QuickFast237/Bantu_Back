<?php

namespace App\Http\Resources\Vendeur;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Produit\ProduitResource;

class VendeurResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'nom' => $this->user->nom,
                'prenom' => $this->user->prenom,
                'email' => $this->user->email,
            ],
            'nom' => $this->nom,
            'description' => $this->description,
            'logo' => $this->logo_img,
            'couverture' => $this->couverture_img,
            'produits' => ProduitResource::collection($this->whenLoaded('produits')),
            'mode_paiements' => ProduitResource::collection($this->whenLoaded('produits')),
            'produits' => ProduitResource::collection($this->whenLoaded('produits')),
        ];
    }
}
