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
        Schema::create('recitation_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('halaka_id')->constrained('halakas')->onDelete('cascade');
            // الطالب الذي تُجرى له الجلسة
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            // تاريخ ووقت الجلسة
            $table->dateTime('session_date');

            // أهداف الجلسة
            $table->string('start_surah');
            $table->integer('start_ayah');
            $table->integer('start_page');
            $table->string('end_surah');
            $table->integer('end_ayah');
            $table->integer('end_page');
            $table->integer('target_pages')->virtualAs('ABS(IFNULL(end_page, start_page) - start_page)');

            //  hadi njibhem me json fichier 
            $table->string('actual_end_surah')->nullable();
            $table->integer('actual_end_ayah')->nullable();
            $table->integer('actual_end_page')->nullable();
            // virtualAs  قادر تكون  null
            $table->integer('actual_pages')->virtualAs('ABS(IFNULL(actual_end_page, start_page) - start_page)');
            $table->decimal('achievement_percentage', 5, 2)->virtualAs('(actual_pages / IFNULL(target_pages, 0)) * 100');

            // 
            $table->integer('tajweed_score')->nullable();
            $table->integer('fluency_score')->nullable();
            $table->integer('memory_score')->nullable();
            $table->text('evaluation_notes')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recitation_sessions');
    }
};
