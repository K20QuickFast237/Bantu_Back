<?php 
namespace App\Http\Resources\Commande;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Produit\ProduitResource;

class CouponResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'montant' => $this->montant_reduction,
            'pourcentage' => $this->pourcentage_reduction,
            'isActive' => $this->isActive
        ];
    }
}