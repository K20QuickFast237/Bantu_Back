<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Professionnel extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'titre_professionnel',
        'email_pro',
        'telephone_pro',
        'nom_entreprise',
        'description_entreprise',
        'site_web',
        'logo',
        'photo_couverture',
        'adresse',
        'ville',
        'pays',
        'num_contribuable',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function offres()
    {
        return $this->hasMany(OffreEmploi::class, 'employeur_id');
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class, 'employeur_id');
    }
}
