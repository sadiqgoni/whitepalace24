<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodDivision extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'description', 'icon'];

    public function foodCreation()
    {
        return $this->hasMany(FoodCreation::class);
    }
}
