<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckOut extends Model
{
    use HasFactory;
    protected $fillable = [
        'check_in_id',
        'guest_name',
        'room_number',
        'check_in_time',
        'check_out_time',
        'total_amount',
        'discount_percentage',
        'discount_amount',
        'additional_charges',
        'restaurant_charge',
        'laundry',
        'late_check_out',
        'car_hire',

        'price_per_night' ,
        'advance_payment' ,
        'laundry_charge' ,
        'car_hire_charge',
        'due_amount' ,
        'payable_amount' ,
        'remaining_amount',
        'change_amount',
        'amount_paying'
    ];
}
