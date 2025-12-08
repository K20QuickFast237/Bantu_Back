<?php 
namespace App\Http\Resources\Vendeur;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Produit\ProduitResource;

class OptionLivraisonResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'description' => $this->description,
            'isActive' => $this->isActive,
            'prix' => $this->pivot->prix
            // 'produits' => ProduitResource::collection($this->produits),
        ];
    }
}