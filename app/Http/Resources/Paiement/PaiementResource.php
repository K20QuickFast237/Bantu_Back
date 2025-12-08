<?php 
namespace App\Http\Resources\Paiement;

use Illuminate\Http\Resources\Json\JsonResource;

class PaiementResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'commande_id' => $this->commande_id,
            'acheteur' => $this->acheteur ? $this->acheteur->user->name : null,
            'montant' => $this->montant,
            'statut' => $this->statut,
            'mode_paiement' => $this->modePaiement ? $this->modePaiement->nom : null,
            'operateur' => $this->operateur ? $this->operateur->nom : null,
            'meta' => $this->meta,
        ];
    }
}