<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProduitAttributValeur extends Model
{
    use HasFactory;

    protected $table = 'mkt_produit_attribut_valeurs';

    protected $fillable = [
        'produit_id',
        'attribut_valeur_id',
        'supplement_cout',
        'stock_qtte'
    ];

    protected $casts = [
        'supplement_cout' => 'decimal:2'
    ];

    public $timestamps = false;

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public function attributValeurs()
    {
        return $this->belongsTo(AttributValeur::class);
    }

    public function commandeproduitAttributs()
    {
        return $this->hasMany(CommandeProduitAttributValeur::class, 'produit_attribut_valeur_id', 'id');
    }
}
