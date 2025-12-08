<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribut extends Model
{
    use HasFactory;

    protected $table = 'mkt_attributs';
    protected $fillable = ['nom'];
    public $timestamps = false;

    public function valeurs()
    {
        return $this->hasMany(AttributValeur::class, 'attribut_id');
    }
}
