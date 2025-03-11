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
        Schema::table('recitation_sessions', function (Blueprint $table) {
            $table->decimal('target_percentage', 5, 2);
            $table->decimal('Progress_percentage', 5, 2)->virtualAs('(target_percentage / achievement_percentage) * 100');
            $table->boolean('present_status')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recitation_sessions', function (Blueprint $table) {
            //
        });
    }
};
