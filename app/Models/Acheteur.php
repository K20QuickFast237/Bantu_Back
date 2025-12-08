<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acheteur extends Model
{
    use HasFactory;

    protected $table = 'mkt_acheteurs';

    protected $fillable = [
        'user_id', 'infos_livraison', 'infos_paiement'
    ];

    protected $casts = [
        'infos_livraison' => 'array',
        'infos_paiement' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paniers()
    {
        return $this->hasMany(Panier::class);
    }

    public function favoris()
    {
        return $this->hasMany(Favori::class, 'acheteur_id');
    }

    public function commandes()
    {
        return $this->hasMany(Commande::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }
}
