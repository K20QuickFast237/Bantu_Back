<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cv extends Model
{
    use HasFactory;

    protected $fillable = ['particulier_id', 'titre', 'fichier'];

    public function particulier()
    {
        return $this->belongsTo(Particulier::class);
    }
}
