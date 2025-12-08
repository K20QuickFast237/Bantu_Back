<?php
namespace App\Http\Resources\Produit;

use Illuminate\Http\Resources\Json\JsonResource;
use \App\Http\Resources\Vendeur\VendeurResource;

class ProduitResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'description' => $this->description,
            'prix' => $this->prix,
            'stock_qtte' => $this->stock_qtte,
            'vendeur' => new VendeurResource($this->whenLoaded('vendeur')),
            'categorie' => $this->categorie ? new CategorieProduitResource($this->whenLoaded('categorie')) : null,
            'medias' => ProduitMediaResource::collection($this->whenLoaded('medias')),
            // 'documents' => $this->documents, // si nécessaire, créer ProduitDocumentResource
            'attributs' => $this->attributValeurs->map(function($valeur) {
                return [
                    'id' => $valeur->id,
                    'attribut' => $valeur->attribut->nom,
                    'valeur' => $valeur->nom,
                    'supplement_cout' => $valeur->pivot->supplement_cout ?? 0,
                    'stock_qtte' => $valeur->pivot->stock_qtte ?? 0
                ];
            }),
        ];
    }
}