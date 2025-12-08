<?php
namespace App\Http\Resources\Produit;

use Illuminate\Http\Resources\Json\JsonResource;
use \App\Http\Resources\Vendeur\VendeurResource;

class SimpleProduitResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'description' => $this->description,
            'prix' => $this->prix,
            'stock_qtte' => $this->stock_qtte,
            'vendeur' => new VendeurResource($this->vendeur),
            'categorie' => $this->categorie ? new CategorieProduitResource($this->categorie) : null,
            'medias' => ProduitMediaResource::collection($this->medias),
        ];
    }
}