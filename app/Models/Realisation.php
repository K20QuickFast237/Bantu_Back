<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Realisation extends Model
{
    /** @use HasFactory<\Database\Factories\RoleFactory> */
    use HasFactory;

    protected $fillable = ['titre', 'description', 'date_realisation', 'localisation', 'lien', 'freelancer_id'];
    
    /**
     * users
     */
    public function freelancer()
    {
        return $this->belongsTo(Freelancer::class);
    }

    public function medias()
    {
        return $this->hasMany(RealisationMedia::class);
    }
}
