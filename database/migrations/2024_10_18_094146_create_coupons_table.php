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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();  
            $table->string('description')->nullable(); 
            $table->string('discount_type')->nullable();  
            $table->decimal('discount_amount',10,2)->nullable(); 
            $table->string('discount_percentage')->nullable(); 
            $table->integer('usage_limit')->default(1); 
            $table->integer('times_used')->default(0); 
            $table->date('valid_from')->nullable(); 
            $table->date('valid_until')->nullable(); 
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
