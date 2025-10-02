<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','name','logo','banner','description','location','status'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function products() {
        return $this->hasMany(Product::class);
    }

    public function deliveryMethods()
{
    return $this->belongsToMany(DeliveryMethod::class, 'shop_delivery')
                ->withPivot('price', 'duration')
                ->withTimestamps();
}

}
