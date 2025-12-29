<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MissionMedia extends Model
{
    use HasFactory;

    protected $table = 'mission_medias';

    protected $fillable = ['mission_id', 'media_type', 'media_path'];

    public function mission()
    {
        return $this->belongsTo(Mission::class);
    }
}

