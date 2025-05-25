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
            $table->integer('start_surah_id')->nullable()->change();
            $table->integer('start_ayah_id')->nullable()->change();
            $table->integer('end_surah_id')->nullable()->change();
            $table->integer('end_ayah_id')->nullable()->change();
            $table->decimal('target_percentage', 5, 2)->nullable()->change();

            $table->dropColumn('present_status');

            $table->string('present');
            $table->string('recitation_type')->nullable();
            $table->string('recitation_narration')->nullable();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('recitation_sessions', function (Blueprint $table) {
            // Revert nullable changes back to NOT NULL
            $table->integer('start_surah_id')->nullable(false)->change();
            $table->integer('start_ayah_id')->nullable(false)->change();
            $table->integer('end_surah_id')->nullable(false)->change();
            $table->integer('end_ayah_id')->nullable(false)->change();
            $table->decimal('target_percentage', 5, 2)->nullable(false)->change();

            // Re-add dropped column
            $table->string('present_status');

            // Drop newly added columns
            $table->dropColumn(['present', 'recitation_type', 'recitation_narration']);
        });
    }

};
