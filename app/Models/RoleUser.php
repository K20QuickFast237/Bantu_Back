<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class RoleUser extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'role_id'
    ];
}
