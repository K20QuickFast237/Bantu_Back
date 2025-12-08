<?php 
namespace App\Http\Resources\Commande;

use App\Http\Resources\Produit\ProduitAttributValeurResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Produit\SimpleProduitResource;
use App\Http\Resources\Produit\ProduitResource;

class CommandeProduitResource extends JsonResource
{
    public function toArray($request)
    {
        $allAttributs = $this->produit->attributValeurs;
        $selectedAttributs = $this->produitAttributValeurs;
        // récupérer les attributs sélectionnés pour ce produit dans la commande
            // récupérer tous les attribut_valeur_id de $selectedAttributs
            $selectedIds = $selectedAttributs->pluck('attribut_valeur_id')->toArray();
            // puis filtrer $allAttributs pour ne garder que ceux dont l'id est dans la liste récupérée
            $allAttributs = $allAttributs->filter(function($valeur) use ($selectedIds) {
                return in_array($valeur->pivot->attribut_valeur_id, $selectedIds);
            })->values();
        
        return [
            // 'produit' => new ProduitResource($this->produit),
            'produit' => new SimpleProduitResource($this->produit),
            'attributs' => $allAttributs->map(function($valeur) {
                return [
                    'attribut' => $valeur->attribut->nom,
                    'valeur' => $valeur->nom,
                    'supplement_cout' => $valeur->pivot->supplement_cout ?? 0,
                    'stock_qtte' => $valeur->pivot->stock_qtte ?? 0
                ];
            }),
            'quantite' => $this->quantite,
            'prix_unitaire' => $this->prix_unitaire,
            'prix_total' => $this->prix_total,
        ];
    }
}