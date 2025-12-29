<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Freelancer extends Model
{
    use HasFactory;

    // protected $table = 'freelancers';

    protected $fillable = [
        'user_id',
        'nom_complet',
        'titre_pro',
        'email_pro',
        'telephone',
        'adresse',
        'ville',
        'pays',
        'photo_profil',
        'photo_couverture',
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function notes()
    {
        return $this->hasMany(FreelancerNote::class);
    }

    public function missions()
    {
        return $this->hasMany(Mission::class);
    }

    public function realisations()
    {
        return $this->hasMany(Realisation::class);
    }
}
