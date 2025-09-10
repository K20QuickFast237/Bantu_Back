<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SkillUser extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_skill';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'skill_id',
        'niveau',
        'created_at',
        'updated_at'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

}
