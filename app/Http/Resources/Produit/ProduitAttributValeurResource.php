<?php
namespace App\Http\Resources\Produit;

use Illuminate\Http\Resources\Json\JsonResource;
use \App\Http\Resources\Vendeur\VendeurResource;

class ProduitAttributValeurResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "attribut" => new AttributResource($this->attributValeur),
            "valeur" => 0,
        ];
    }
}