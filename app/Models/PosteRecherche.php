<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosteRecherche extends Model
{
    protected $fillable = [
        'user_id',
        'skills',
        'localisations',
        'type_contrats',
        'niveau_experience',
    ];
    
}
