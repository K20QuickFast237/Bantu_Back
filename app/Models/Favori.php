<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favori extends Model
{
    use HasFactory;

    protected $table = 'mkt_favoris';

    protected $fillable = [
        'acheteur_id', 'produit_id'
    ];

    public $timestamps = false;

    public function acheteur()
    {
        return $this->belongsTo(Acheteur::class);
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }
}
