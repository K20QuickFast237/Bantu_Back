<?php 
namespace App\Services;

use App\Models\Paiement;
use Illuminate\Support\Str;

class PaiementService
{
    public function initiatePaiement($acheteur, array $data)
    {
        $paiement = Paiement::create([
            'commande_id' => $data['commande_id'],
            'acheteur_id' => $acheteur->id,
            'mode_paiement_id' => $data['mode_paiement_id'],
            'operateur_id' => $data['operateur_id'],
            'montant' => 0, // à calculer depuis la commande
            'statut' => 'en_attente',
            'reference' => Str::uuid(),
            'meta' => []
        ]);

        // Logique externe pour paiement via API opérateur
        // Exemple: $this->callPaymentGateway($paiement);

        return $paiement;
    }

    public function verifyPaiement(Paiement $paiement)
    {
        // Logique de vérification avec l'opérateur
        $paiement->statut = 'paye';
        $paiement->save();

        return $paiement;
    }
}