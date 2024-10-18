<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffManagement extends Model
{
    use HasFactory;


    protected $fillable = [
        'full_name',
        'email',
        'phone_number',
        'role',
        'status',
        'employment_date',
        'termination_date',
        'profile_picture',
        'address',
        'date_of_birth',
        'shift',
        'next_of_kin_name',
        'next_of_kin_address',
        'next_of_kin_phone_number'
    ];
}
