<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodDivision extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'description', 'icon'];

    // public function menuItems()
    // {
    //     return $this->hasMany(MenuItem::class);
    // }
}
