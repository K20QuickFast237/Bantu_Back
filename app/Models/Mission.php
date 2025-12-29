<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mission extends Model
{
    use HasFactory;

    protected $fillable = ['freelancer_id', 'client_id', 'titre', 'description', 'date_debut', 'date_fin', 'cout', 'statut'];

    public function freelancer()
    {
        return $this->belongsTo(Freelancer::class);
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function medias()
    {
        return $this->hasMany(MissionMedia::class);
    }
}

