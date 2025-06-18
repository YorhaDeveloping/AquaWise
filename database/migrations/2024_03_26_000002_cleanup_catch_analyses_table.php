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
            // Remove review-related columns if they exist
            if (Schema::hasColumn('catch_analyses', 'reviewed')) {
                $table->dropColumn('reviewed');
            }
            if (Schema::hasColumn('catch_analyses', 'reviewer_id')) {
                $table->dropForeign(['reviewer_id']);
                $table->dropColumn('reviewer_id');
            }
            if (Schema::hasColumn('catch_analyses', 'reviewed_at')) {
                $table->dropColumn('reviewed_at');
            }
            if (Schema::hasColumn('catch_analyses', 'expert_feedback')) {
                $table->dropColumn('expert_feedback');
            }
            if (Schema::hasColumn('catch_analyses', 'recommendations')) {
                $table->dropColumn('recommendations');
            }
            if (Schema::hasColumn('catch_analyses', 'sustainability_rating')) {
                $table->dropColumn('sustainability_rating');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('catch_analyses', function (Blueprint $table) {
            $table->boolean('reviewed')->default(false);
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('expert_feedback')->nullable();
            $table->text('recommendations')->nullable();
            $table->string('sustainability_rating')->nullable();
        });
    }
}; 