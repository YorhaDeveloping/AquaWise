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
        Schema::table('catch_analyses', function (Blueprint $table) {
            $table->text('expert_feedback')->nullable();
            $table->text('recommendations')->nullable();
            $table->string('sustainability_rating')->nullable(); // e.g., 'Good', 'Concerning', 'Critical'
            $table->foreignId('reviewer_id')->nullable()->unique()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('catch_analyses', function (Blueprint $table) {
            $table->dropForeign(['reviewer_id']);
            $table->dropUnique(['reviewer_id']);
            $table->dropColumn([
                'expert_feedback',
                'recommendations',
                'sustainability_rating',
                'reviewer_id',
                'reviewed_at'
            ]);
        });
    }
};
