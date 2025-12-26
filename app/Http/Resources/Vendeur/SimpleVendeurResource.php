<?php

namespace App\Http\Resources\Vendeur;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Produit\ProduitResource;

class SimpleVendeurResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
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
