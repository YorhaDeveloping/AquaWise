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
        Schema::create('fish_guides', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('fish_species');
            $table->text('care_instructions');
            $table->text('feeding_guide');
            $table->json('water_parameters');
            $table->text('common_diseases')->nullable();
            $table->text('prevention_tips')->nullable();
            $table->enum('status', ['draft', 'published', 'archived', 'disabled'])->default('draft');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('views')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fish_guides');
    }
};