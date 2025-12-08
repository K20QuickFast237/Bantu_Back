<?php 
namespace App\Http\Resources\Acheteur;

use App\Http\Resources\Commande\CommandeProduitResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Produit\ProduitResource;

class PanierResource extends JsonResource
{
    public function toArray($request)
    {
        return (new CommandeProduitResource($this->commandeProduit))
            ->additional(['id' => $this->produit_id]);
        // return [
        //     'id' => $this->id,
        //     'produit' => new CommandeProduitResource($this->commandeProduit)
        // ];
    }
}