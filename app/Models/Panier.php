<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Panier extends Model
{
    use HasFactory;

    protected $table = 'mkt_panier';

    protected $fillable = [
        'acheteur_id', 'commande_produit_id', 'produit_id'
    ];

    public function acheteur()
    {
        return $this->belongsTo(Acheteur::class);
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public function commandeProduit(): BelongsTo
    {
        return $this->belongsTo(CommandeProduit::class, 'commande_produit_id');
    }

    public function flushPanier(Acheteur $acheteur)
    {
        return $this->where('acheteur_id', $acheteur->id)->delete();
    }
}
