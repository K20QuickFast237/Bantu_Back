<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreelancerNote extends Model
{
    use HasFactory;

    protected $table = 'freelancer_notes';

    protected $fillable = [
        'freelancer_id',
        'client_id',
        'mission_id',
        'score',
        'description',
    ];

    public function freelancer()
    {
        return $this->belongsTo(Freelancer::class);
    }

    public function mission()
    {
        return $this->belongsTo(Mission::class);
    }
    
    public function client()
    {
        return $this->belongsTo(User::class);
    }
}
