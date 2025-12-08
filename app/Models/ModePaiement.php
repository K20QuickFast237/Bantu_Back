<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModePaiement extends Model
{
    use HasFactory;

    protected $table = 'mkt_mode_paiements';

    protected $fillable = [
        'nom', 'description', 'isActive'
    ];

    public function operateurs()
    {
        return $this->hasMany(OperateurPaiement::class, 'mode_paiement_id');
    }

    public function commandes()
    {
        return $this->hasMany(Commande::class);
    }

    public function vendeurs()
    {
        return $this->belongsToMany(
            Vendeur::class,
            'mkt_vendeur_mode_paiements',
            'mode_id',
            'vendeur_id'
        );
    }
}
