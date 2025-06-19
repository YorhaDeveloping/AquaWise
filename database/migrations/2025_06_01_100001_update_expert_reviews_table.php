<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expert_reviews', function (Blueprint $table) {
            $table->dropColumn(['expert_feedback', 'recommendations', 'sustainability_rating']);
        });
    }

    public function down(): void
    {
        Schema::table('expert_reviews', function (Blueprint $table) {
            $table->text('expert_feedback');
            $table->text('recommendations');
            $table->string('sustainability_rating');
        });
    }
}; 