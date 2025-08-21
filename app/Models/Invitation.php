<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invitation extends Model
{
    use HasFactory;
    
    protected $table = 'invitations';

    protected $fillable = [
        'candidature_id',
        'employeur_id',
        'date_heure_entretien',
        'type_entretien',
        'lieu',
        'lien_visio',
        'instruction_supl',
        'statut',
        'date_envoi',
    ];

    // La candidature associée
    public function candidature(): BelongsTo
    {
        return $this->belongsTo(Candidature::class);
    }

    // Le professionnel (employeur) qui envoie l'invitation
    public function employeur(): BelongsTo
    {
        return $this->belongsTo(Professionnel::class, 'employeur_id');
    }
}
