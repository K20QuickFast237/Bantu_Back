<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Experience extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'titre_poste',
        'nom_entreprise',
        'date_debut',
        'date_fin',
        'description_taches',
        'adresse',
        'ville',
        'pays',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
