<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommandeProduitAttributValeur extends Model
{
    use HasFactory;

    protected $table = 'mkt_commande_produit_attribut_valeurs';

    protected $fillable = ['commande_produit_id', 'produit_attribut_valeur_id'];

    public $timestamps = false;

    public function produitAttributValeurs()
    {
        return $this->belongsTo(ProduitAttributValeur::class);
    }

    public function commandeProduit()
    {
        return $this->belongsTo(CommandeProduit::class);
    }
}
