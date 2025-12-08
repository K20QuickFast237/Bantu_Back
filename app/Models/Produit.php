<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    use HasFactory;

    protected $table = 'mkt_produits';

    protected $fillable = [
        'vendeur_id', 'categorie_id', 'nom', 'description',
        'prix', 'stock_qtte', 'hasAttributes'
    ];

    protected $casts = [
        'hasAttributes' => 'boolean',
        'prix' => 'decimal:2',
    ];

    public function vendeur()
    {
        return $this->belongsTo(Vendeur::class);
    }

    public function categorie()
    {
        return $this->belongsTo(CategorieProduit::class, 'categorie_id');
    }

    public function medias()
    {
        return $this->hasMany(ProduitMedia::class);
    }

    // public function documents()
    // {
    //     return $this->hasMany(ProduitDocument::class);
    // }

    public function attributValeurs()
    {
        return $this->belongsToMany(
            AttributValeur::class,
            'mkt_produit_attribut_valeurs',
            'produit_id',
            'attribut_valeur_id'
        )->withPivot(['supplement_cout', 'stock_qtte']);
        //  ->withTimestamps();
    }

    public function variantes()
    {
        return $this->hasMany(ProduitAttributValeur::class);
    }

    public function paniers()
    {
        return $this->hasMany(Panier::class)->chaperone();
    }
}
