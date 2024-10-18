<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('staff_management', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('phone_number')->unique();
            $table->string('role')->unique();
            $table->string('status')->unique();
            $table->date('employment_date')->unique();
            $table->date('termination_date')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('address')->unique();
            $table->date('date_of_birth')->unique();
            $table->string('shift')->unique();
            $table->string('next_of_kin_name')->unique();
            $table->string('next_of_kin_address')->unique();
            $table->string('next_of_kin_phone_number')->unique();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_management');
    }
};