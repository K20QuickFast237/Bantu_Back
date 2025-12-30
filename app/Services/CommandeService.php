<?php 
namespace App\Services;

use App\Models\Commande;
use App\Models\Panier;
use App\Models\CommandeProduitValeur as CommandeProduit;
use App\Models\VendeurOptionLivraison;
use Illuminate\Support\Facades\DB;

class CommandeService
{
    public function createCommande($acheteur, array $data)
    {
        return DB::transaction(function() use ($acheteur, $data) {
            // $panierItems = $acheteur->paniers()->with('produit', 'attributValeur')->get();
            $panierItems = $acheteur->paniers()->with('produit', 'commandeProduit')->get();
            
            if ($panierItems->isEmpty()) {
                throw new \Exception("Le panier est vide.");
            }

            $commande = $acheteur->commandes()
                ->where('statut', 'en_attente')
                ->orWhere('statut', 'en_cours')
                ->first();
                
            if (!$commande) {
                throw new \Exception("Aucune commande en attente trouvée pour cet acheteur.");
            }
            $commande->update([
                'acheteur_id' => $acheteur->id,
                'option_livraison_id' => $data['option_livraison_id'],
                'mode_paiement_id' => $data['mode_paiement_id'],
                'adresse_livraison' => $data['adresse_livraison'],
                'ville_livraison' => $data['ville_livraison'],
                'pays_livraison' => $data['pays_livraison'],
                'tel_livraison' => $data['tel_livraison'],
                'statut' => 'en_cours',
            ]);

            $sousTotal = 0;
            $vendeurIds = [];

            foreach ($panierItems as $item) {
                $sousTotal += $item->commandeProduit->prix_total;
                $vendeurIds[] = $item->produit->vendeur_id;
            }

            $fraisLivraison = 0;
            $vendeurIds = array_unique($vendeurIds);
            foreach ($vendeurIds as $vendeurId) {
                // Calculer les frais de livraison pour chaque vendeur
                $optionLivraison = VendeurOptionLivraison::where('vendeur_id', $vendeurId)
                    ->where('option_id', $data['option_livraison_id'])
                    ->select('prix')
                    ->first();
                $fraisLivraison += $optionLivraison ? $optionLivraison->prix : 0;
            }

            $commande->sous_total = $sousTotal;
            $commande->frais_livraison = $fraisLivraison;
            $commande->total = $commande->sous_total + $commande->frais_livraison;
            $commande->save();

            // Vider le panier Non à faire seulement après le paiement de la commande
            // $panierModel = new Panier();
            // $panierModel->flushPanier($acheteur);

            return $commande;
        });
    }
}