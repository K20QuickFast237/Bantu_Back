<?php 
namespace App\Http\Resources\Acheteur;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Produit\ProduitResource;

class SimpleAcheteurResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'infos_livraison' => $this->infos_livraison,
            'infos_paiement' => $this->infos_paiement
        ];
    }
}