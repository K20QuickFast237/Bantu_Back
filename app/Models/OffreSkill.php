<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OffreSkill extends Model
{
    use HasFactory;
    
    protected $table = 'offre_skills';

    protected $fillable = [
        'offre_id',
        'skill_id',
        'ordre_aff',
    ];
}
