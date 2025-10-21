<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Categorie extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'slug',
        'description',
    ];

    public function offres()
    {
        return $this->hasMany(OffreEmploi::class, 'categorie_id');
    }
}
