<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributValeur extends Model
{
    use HasFactory;

    protected $table = 'mkt_attribut_valeurs';
    protected $fillable = ['attribut_id', 'nom'];

    public function attribut()
    {
        return $this->belongsTo(Attribut::class);
    }

    public function produitAttributValeurs()
    {
        return $this->hasMany(ProduitAttributValeur::class, 'attribut_valeur_id', 'id');
    }

    // public function produits()
    // {
    //     return $this->belongsToMany(
    //         Produit::class,
    //         'produit_attribut_valeurs',
    //         'attribut_valeur_id',
    //         'produit_id'
    //     )->withPivot(['supplement_cout', 'stock_qtte'])
    //      ->withTimestamps();
    // }

    // public function variantes()
    // {
    //     return $this->hasMany(ProduitAttributValeur::class, 'attribut_valeur_id');
    // }
}
