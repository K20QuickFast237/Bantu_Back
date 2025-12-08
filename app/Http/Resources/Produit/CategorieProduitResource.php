<?php 
namespace App\Http\Resources\Produit;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Produit\ProduitResource;

class CategorieProduitResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'description' => $this->description,
            'statut' => $this->statut,
            // 'produits' => ProduitResource::collection($this->produits),
        ];
    }
}