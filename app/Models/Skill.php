<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'icon',
        'nbr_usage',
    ];

    public function offres()
    {
        return $this->belongsToMany(OffreEmploi::class, 'offre_skill', 'skill_id', 'offre_id')
                    ->using(OffreSkill::class)
                    ->withPivot('ordre_aff')
                    ->withTimestamps();
    }

}
