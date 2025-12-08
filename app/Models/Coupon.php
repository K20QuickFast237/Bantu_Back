<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $table = 'mkt_coupons';

    protected $fillable = [
        'code', 'montant_reduction', 'pourcentage_reduction',
        'date_expiration', 'nbr_depart', 'nbr_restant', 'isActive'
    ];

    protected $casts = [
        'montant_reduction' => 'decimal:2',
        'pourcentage_reduction' => 'integer',
        'date_expiration' => 'datetime',
        'isActive' => 'boolean'
    ];

    public function vendeur()
    {
        return $this->belongsTo(Vendeur::class);
    }

    public function commandes()
    {
        return $this->hasMany(Commande::class, 'coupon_id');
    }
}
