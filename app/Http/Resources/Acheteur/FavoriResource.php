<?php 
namespace App\Http\Resources\Acheteur;

use App\Http\Resources\Commande\CommandeProduitResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Produit\ProduitResource;
use App\Http\Resources\Produit\SimpleProduitResource;

class FavoriResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'produit' => new SimpleProduitResource($this->produit)
        ];
    }
}