<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendeurOptionLivraison extends Model
{
    use HasFactory;

    protected $table = 'mkt_vendeur_option_livraisons';

    protected $fillable = [
        'vendeur_id', 'option_id', 'prix'
    ];

    public $timestamps = false;
}
