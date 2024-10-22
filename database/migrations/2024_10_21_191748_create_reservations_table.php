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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string(column: 'reservation_number')->unique();
            $table->foreignId('guest_id')->nullable()->constrained('guests')->cascadeOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->cascadeOnDelete();
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->cascadeOnDelete();
            $table->date(column: 'check_in_date')->nullable();
            $table->date('check_out_date')->nullable();
            $table->decimal('total_amount',12,2)->nullable();
            $table->decimal('amount_paid',12,2)->nullable();
            $table->string('payment_method')->nullable();
            $table->decimal('remaining_balance',12,2)->nullable();
            $table->string('coupon_discount')->nullable();
            $table->string('payment_status')->nullable();
            $table->decimal('price_per_night',12,2)->nullable();
            $table->string('frequent_guest_message')->nullable();
            $table->string('number_of_nights')->nullable();
            $table->string('status')->nullable();
            $table->text('special_requests')->nullable(); 
            $table->string('number_of_people')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
