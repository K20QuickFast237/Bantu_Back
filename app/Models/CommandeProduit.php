<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommandeProduit extends Model
{
    use HasFactory;

    protected $table = 'mkt_commande_produits';

    protected $fillable = [
        'commande_id', 'produit_id', // 'attribut_valeurs',
        'quantite', 'prix_unitaire', 'prix_total'
    ];

    protected $casts = [
        'prix_unitaire' => 'decimal:2',
        'prix_total' => 'decimal:2'
    ];

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public function cmdProdAttrVals()
    {
        return $this->hasMany(CommandeProduitAttributValeur::class, 'commande_produit_id', 'id');
    }

    public function produitAttributValeurs()
    {
        return $this->belongsToMany(
            ProduitAttributValeur::class,
            'mkt_commande_produit_attribut_valeurs',
            'commande_produit_id',
            'produit_attribut_valeur_id'
        );
    }

    // public function panierItems()
    // {
    //     return $this->hasOne(Panier::class, 'commande_produit_id');
    // }

}
