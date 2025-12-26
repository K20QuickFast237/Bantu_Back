<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use HasFactory;

    protected $table = 'mkt_commandes';
    protected $fillable = [
        'acheteur_id', 'option_livraison_id', 'mode_paiement_id',
        'statut', 'adresse_livraison', 'ville_livraison', 'pays_livraison', 'tel_livraison',
        'sous_total', 'frais_livraison', 'total', 'coupon_id', 'reduction', 'net_a_payer'
    ];

    protected $casts = [
        'sous_total' => 'decimal:2',
        'frais_livraison' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    public function acheteur()
    {
        return $this->belongsTo(Acheteur::class);
    }

    public function optionLivraison()
    {
        return $this->belongsTo(OptionLivraison::class, 'option_livraison_id');
    }

    public function modePaiement()
    {
        return $this->belongsTo(ModePaiement::class, 'mode_paiement_id');
    }

    public function produits()
    {
        return $this->hasMany(CommandeProduit::class, 'commande_id');
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class, 'commande_id');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function applyCoupon(Coupon $coupon)
    {
        // Verify Availability
        if ($coupon->date_expiration && ($coupon->date_expiration < now())) {
            throw new \Exception("Le coupon a expiré.");
        }elseif ($coupon->nbr_restant !== null && $coupon->nbr_restant <= 0) {
            throw new \Exception("Le coupon n'est plus disponible.");
        }

        $this->coupon_id = $coupon->id;

        if ($coupon->montant_reduction) {
            $reduction = $coupon->montant_reduction;
        } elseif ($coupon->pourcentage_reduction) {
            $reduction = ($coupon->pourcentage_reduction / 100) * $this->total;
        } else {
            $reduction = 0;
        }

        $this->reduction = $reduction;
        $this->net_a_payer = $this->total - $reduction;
        $this->save();

        // Actualiser le coupon
        if ($coupon->nbr_restant !== null) {
            $coupon->nbr_restant -= 1;
            $coupon->save();
        }
    }
}
