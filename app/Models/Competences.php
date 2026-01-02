<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competences extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'description'];

    public $timestamps = false;

    public function freelancers()
    {
        return $this->belongsToMany(Freelancer::class, 'freelancer_competences', 'competence_id', 'freelancer_id')
            ->using(FrelancerCompetences::class)
            ->withPivot('niveau');
    }
}
