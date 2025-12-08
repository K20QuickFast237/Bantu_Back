<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;

    protected $table = 'mkt_paiements';

    protected $fillable = [
        'commande_id', 'acheteur_id', 'mode_paiement_id', 'operateur_id',
        'montant', 'statut', 'meta'
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'meta' => 'array'
    ];

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    public function acheteur()
    {
        return $this->belongsTo(Acheteur::class);
    }

    public function modePaiement()
    {
        return $this->belongsTo(ModePaiement::class);
    }

    public function operateur()
    {
        return $this->belongsTo(OperateurPaiement::class, 'operateur_id');
    }
}
