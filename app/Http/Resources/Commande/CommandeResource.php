<?php 
namespace App\Http\Resources\Commande;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Acheteur\AcheteurResource;
use App\Http\Resources\Commande\CommandeProduitResource;
use App\Http\Resources\Paiement\PaiementResource;
use App\Http\Resources\Vendeur\OptionLivraisonResource;
use App\Http\Resources\Vendeur\ModePaiementResource;

class CommandeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'acheteur' => new AcheteurResource($this->acheteur),
            'statut' => $this->statut,
            'adresse_livraison' => $this->adresse_livraison,
            'ville_livraison' => $this->ville_livraison,
            'pays_livraison' => $this->pays_livraison,
            'tel_livraison' => $this->tel_livraison,
            'sous_total' => $this->sous_total,
            'frais_livraison' => $this->frais_livraison,
            'total' => $this->total,
            'date_commande' => $this->created_at->format('Y-m-d H:i:s'),
            'produits' => CommandeProduitResource::collection($this->whenLoaded('produits')),
            'paiements' => PaiementResource::collection($this->whenLoaded('paiements')),
            'option_livraison' => new OptionLivraisonResource($this->whenLoaded('optionLivraison')),
            'mode_paiement' => new ModePaiementResource($this->whenLoaded('modePaiement')),
            'coupon' => new CouponResource($this->whenLoaded('coupon')),
        ];
    }
}