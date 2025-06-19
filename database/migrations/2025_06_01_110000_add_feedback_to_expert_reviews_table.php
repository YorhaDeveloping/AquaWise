<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expert_reviews', function (Blueprint $table) {
            $table->text('feedback')->nullable()->after('reviewer_id');
        });
    }

    public function down(): void
    {
        Schema::table('expert_reviews', function (Blueprint $table) {
            $table->dropColumn('feedback');
        });
    }
}; 