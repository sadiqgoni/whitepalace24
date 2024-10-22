<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'discount_type', 
        'discount_amount',
        'discount_percentage',
        'valid_from',
        'valid_until',
        'usage_limit',
        'times_used',
        'status',
    ];

    public function reservation()
    {
        return $this->hasMany(Reservation::class);
    }
    public function groupReservation()
    {
        return $this->hasMany(GroupReservation::class);
    }
}
