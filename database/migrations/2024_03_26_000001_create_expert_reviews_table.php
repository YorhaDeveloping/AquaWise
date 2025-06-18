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
        Schema::create('expert_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catch_analysis_id')->constrained()->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
            $table->text('expert_feedback');
            $table->text('recommendations');
            $table->string('sustainability_rating'); // e.g., 'Good', 'Concerning', 'Critical'
            $table->timestamps();
            
            // Ensure one expert can only review once
            $table->unique(['catch_analysis_id', 'reviewer_id']);
        });

        // Remove old columns from catch_analyses table
        Schema::table('catch_analyses', function (Blueprint $table) {
            $table->dropForeign(['reviewer_id']);
            $table->dropColumn([
                'expert_feedback',
                'recommendations',
                'sustainability_rating',
                'reviewer_id',
                'reviewed_at',
                'reviewed'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore columns to catch_analyses table
        Schema::table('catch_analyses', function (Blueprint $table) {
            $table->text('expert_feedback')->nullable();
            $table->text('recommendations')->nullable();
            $table->string('sustainability_rating')->nullable();
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->boolean('reviewed')->default(false);
        });

        Schema::dropIfExists('expert_reviews');
    }
}; 