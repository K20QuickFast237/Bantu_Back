<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryMethod extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function shops()
    {
        return $this->belongsToMany(Shop::class, 'shop_delivery')
                    ->withPivot('price', 'duration')
                    ->withTimestamps();
    }
}
