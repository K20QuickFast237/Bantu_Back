<?php 
namespace App\Http\Resources\Acheteur;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Produit\ProduitResource;

class AcheteurResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'nom' => $this->user->nom,
                'prenom' => $this->user->prenom,
                'email' => $this->user->email,
            ],
            'infos_livraison' => $this->infos_livraison,
            'infos_paiement' => $this->infos_paiement
        ];
    }
}