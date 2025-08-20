<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OffreEmploi extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'employeur_id', 
        'titre_poste', 
        'description_poste', 
        'exigences', 
        'responsabilites',
        'ville', 
        'pays', 
        'type_contrat', 
        'remuneration_min', 
        'remuneration_max',
        'date_publication', 
        'date_limite_soumission', 
        'statut', 
        'nombre_vues'
    ];

    
    public function employeur(): BelongsTo
    {
        return $this->belongsTo(Professionnel::class, 'employeur_id');
    }

    
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'offre_skills', 'offre_id', 'skill_id')
                    ->withPivot('ordre_aff')
                    ->withTimestamps();
    }

    
    public function candidatures(): HasMany
    {
        return $this->hasMany(Candidature::class, 'offre_id');
    }
}
