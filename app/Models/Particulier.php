<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Particulier extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date_naissance',
        'telephone',
        'adresse',
        'ville',
        'pays',
        'titre_professionnel',
        'resume_profil',
        'image_profil',
        'cv_link',
        'lettre_motivation_link',
        'portfolio_link',
        'ressources',
        'is_visible',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function candidatures()
    {
        return $this->hasMany(Candidature::class);
    }

    public function cvs()
    {
        return $this->hasMany(Cv::class);
    }
}
