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
        Schema::create('food_creations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('food_division_id')->constrained()->cascadeOnDelete();  
            $table->string('name'); 
            $table->text('description')->nullable();  
            $table->decimal('price', 10, 2);  
            $table->string('image')->nullable();  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_creations');
    }
};
