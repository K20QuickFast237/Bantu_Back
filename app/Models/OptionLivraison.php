<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OptionLivraison extends Model
{
    use HasFactory;

    protected $table = 'mkt_option_livraisons';

    protected $fillable = [
        'nom', 'description', 'isActive'
    ];

    protected $casts = [
        'isActive' => 'boolean'
    ];

    public $timestamps = false;

    public function vendeurs()
    {
        return $this->belongsToMany(
            Vendeur::class,
            'mkt_vendeur_option_livraisons',
            'option_id',
            'vendeur_id'
        );
    }
}
