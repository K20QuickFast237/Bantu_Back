<?php 
namespace App\Http\Resources\Commande;

use App\Http\Resources\Produit\ProduitAttributValeurResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Produit\SimpleProduitResource;
use App\Http\Resources\Produit\ProduitResource;

class CmdProdAttValResource extends JsonResource
{
    public function toArray($request)
    {
        return new ProduitAttributValeurResource($this->produitAttributValeurs);
    }
}