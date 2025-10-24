<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OffreCategorie extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'slug',
        'description',
    ];

    public function offres(): HasMany
    {
        return $this->hasMany(OffreEmploi::class, 'categorie_id');
    }
}
