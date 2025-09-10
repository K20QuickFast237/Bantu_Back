<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'icon',
        'nbr_usage',
    ];
    
    /**
     * offres
     */
    public function offres(): BelongsToMany
    {
        return $this->belongsToMany(OffreEmploi::class, 'offre_skill', 'skill_id', 'offre_id')
                    ->using(OffreSkill::class)
                    ->withPivot('ordre_aff')
                    ->withTimestamps();
    }

    /**
     * users
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('niveau')->using(SkillUser::class);
    }
}
