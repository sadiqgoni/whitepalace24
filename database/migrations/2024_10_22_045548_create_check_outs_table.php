<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('check_outs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('check_in_id')->constrained('check_ins')->onDelete('cascade');
            $table->string('guest_name')->nullable();
            $table->string('room_number')->nullable();
            $table->dateTime('check_in_time')->nullable();
            $table->dateTime('check_out_time')->nullable();
            $table->decimal('total_amount', 12, 2)->nullable();
            $table->decimal('discount_percentage', 5, 2)->default(0)->nullable();
            $table->decimal('discount_amount', 12, 2)->default(0)->nullable();
            $table->decimal('additional_charges', 12, 2)->default(0)->nullable();
            $table->decimal('restaurant_charge', 12, 2)->default(0)->nullable();
            $table->string('late_check_out')->nullable();
            $table->decimal('car_hire_charge',12,2)->nullable();
            $table->decimal('laundry_charge',12,2)->nullable();
            $table->decimal('advance_payment', 12, 2)->nullable();
            $table->decimal('price_per_night', 12, 2)->nullable();
            $table->decimal('due_amount', 12, 2)->nullable();
            $table->decimal('payable_amount', 12, 2)->nullable();
            $table->decimal('remaining_amount', 12, 2)->nullable();
            $table->decimal('change_amount', 12, 2)->nullable();
            $table->decimal('amount_paying', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('check_outs');
    }
};
