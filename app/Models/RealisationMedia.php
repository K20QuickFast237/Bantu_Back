<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RealisationMedia extends Model
{
    use HasFactory;

    protected $table = 'realisation_medias';

    protected $fillable = ['realisation_id', 'media_type', 'media_path'];

    public function realisation()
    {
        return $this->belongsTo(Realisation::class);
    }
}

