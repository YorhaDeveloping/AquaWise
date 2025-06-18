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
        Schema::create('catch_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('fish_species');
            $table->integer('quantity');
            $table->decimal('total_weight', 8, 2); // Weight in kg with 2 decimal places
            $table->decimal('average_size', 8, 2)->nullable(); // Average size in cm
            $table->string('location');
            $table->date('catch_date');
            $table->string('weather_conditions')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('reviewed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catch_analyses');
    }
}; 