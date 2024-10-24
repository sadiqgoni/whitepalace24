<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodCreation extends Model
{
    use HasFactory;
    protected $fillable = ['food_division_id', 'name', 'description', 'price', 'image',];

    public function foodDivision()
    {
        return $this->belongsTo(FoodDivision::class);
    }
    public function orderItem()
    {
        return $this->hasMany(OrderItem::class);
    }
}
