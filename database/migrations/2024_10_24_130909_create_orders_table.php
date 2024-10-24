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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string(column: 'invoice_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('dine_table_id')->nullable()->constrained('dine_tables')->cascadeOnDelete();  
            $table->string(column: 'guest_info')->nullable();
            $table->string(column: 'room_number')->nullable();
            $table->decimal('amount_paid', 12, 2)->nullable();
            $table->decimal('service_charge', 12, 2)->nullable();
            $table->decimal('total_amount', 12, 2)->nullable();
            $table->string(column: 'status')->default('pending');
            $table->string(column: 'customer_type')->nullable();
            $table->decimal('change_amount', 12, 2)->nullable();
            $table->string(column: 'payment_method')->nullable();
            $table->string(column: 'dining_option')->nullable();
            $table->string(column: 'billing_option')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
