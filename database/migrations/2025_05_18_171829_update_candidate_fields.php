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
        Schema::table('candidates', function (Blueprint $table) {
            $table->json('desired_recitations')->nullable()->change();
            $table->integer('self_evaluation')->nullable()->change();
            $table->string('qualification_file')->nullable()->change();
            $table->string('audio_recitation')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->json('desired_recitations')->change();
            $table->integer('self_evaluation')->change();
            $table->string('qualification_file')->change();
            $table->string('audio_recitation')->change();
        });

    }
};
