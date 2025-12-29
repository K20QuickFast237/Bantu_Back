<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RealisationImage extends Model
{
    /** @use HasFactory<\Database\Factories\RoleFactory> */
    use HasFactory;

    protected $fillable = [
        'realisation_id', 
        'image',
    ];
    
    /**
     * users
     */
    public function realisation()
    {
        return $this->belongsTo(Realisation::class);
    }
}
