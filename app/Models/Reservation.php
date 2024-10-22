<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    
    protected $fillable = [
        'reservation_number',
        'guest_id',
        'room_id',
        'check_in_date',
        'check_out_date',
        'number_of_people',
        'price_per_night',
        'total_amount',
        'amount_paid',
        'payment_method',
        'coupon_id',
        'coupon_discount',
        'frequent_guest_message',
        'number_of_nights',
        'status',
        'payment_status',
        'special_requests',
        'remaining_balance',
    ];


    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
  
}
