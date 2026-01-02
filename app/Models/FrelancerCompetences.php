<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FrelancerCompetences extends Model
{
    use HasFactory;

    protected $table = 'freelancer_competences';

    protected $fillable = ['freelancer_id', 'competence_id', 'niveau'];

    public $timestamps = false;

    public function freelancer()
    {
        return $this->belongsTo(Freelancer::class);
    }

}
