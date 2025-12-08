<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendeur extends Model
{
    use HasFactory;

    protected $table = 'mkt_vendeurs';

    protected $fillable = [
        'user_id', 'nom', 'slogan', 'description', 'logo_img',
        'couverture_img', 'email', 'telephone', 'autre_contact',
        'adresse', 'ville', 'pays', 'num_contribuable', 'statut'
    ];

    // protected $casts = [
    //     'pays' => 'array',
    // ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function produits()
    {
        return $this->hasMany(Produit::class);
    }

    public function optionsLivraisons()
    {
        return $this->belongsToMany(
            OptionLivraison::class,
            'mkt_vendeur_option_livraisons',
            'vendeur_id',
            'option_id'
        )->withPivot('prix');
    }
    public function modePaiements()
    {
        return $this->belongsToMany(
            ModePaiement::class,
            'mkt_vendeur_mode_paiements',
            'vendeur_id',
            'mode_id'
        );
    }

    public function coupons()
    {
        return $this->hasMany(Coupon::class);
    }
}
