<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidature extends Model
{
    use HasFactory;

    protected $table = 'candidatures';

    protected $fillable = [
        'particulier_id',
        'offre_id',
        'statut',
        'cv_url',
        'motivation_url',
        'cv_genere',
        'motivation_text',
        'note_ia',
        'commentaire_employeur',
        'autres_documents',
    ];

    protected $casts = [
        'cv_genere' => 'array',
    ];

    // Le particulier (candidat)
    public function particulier(): BelongsTo
    {
        return $this->belongsTo(Particulier::class);
    }

    // L’offre d’emploi
    public function offre(): BelongsTo
    {
        return $this->belongsTo(OffreEmploi::class, 'offre_id');
    }

    // Invitations liées à cette candidature
    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }
}
