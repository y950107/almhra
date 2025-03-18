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
            $table->foreignId('student_id')->constrained()->onDelete('cascade'); 
            $table->date('session_date'); 
    
            $table->integer('start_surah_id');
            $table->integer('start_ayah_id');
            $table->integer('end_surah_id');
            $table->integer('end_ayah_id');
            $table->integer('start_page')->nullable();
            $table->integer('end_page')->nullable();
            $table->integer('target_lines')->nullable();
            $table->integer('target_pages')->nullable();
            $table->integer('actual_end_surah_id')->nullable();
            $table->integer('actual_end_ayah_id')->nullable();
            $table->integer('actual_end_page')->nullable();
            $table->integer('actuel_lines')->nullable();
            $table->integer('actual_pages')->nullable(); 
            $table->decimal('achievement_percentage', 5, 2)->virtualAs('(actual_pages / IFNULL(target_pages, 1)) * 100');
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















// old  one 

// public function up(): void
// {
//     Schema::create('recitation_sessions', function (Blueprint $table) {
//         $table->id();
//         $table->foreignId('halaka_id')->constrained('halakas')->onDelete('cascade');
//         $table->foreignId('student_id')->constrained()->onDelete('cascade'); 
//         $table->date('session_date'); 

        
//         $table->foreignId('start_surah_id')->constrained('surahs')->onDelete('cascade');
//         $table->foreignId('start_ayah_id')->constrained('verses')->onDelete('cascade');
//         $table->foreignId('end_surah_id')->constrained('surahs')->onDelete('cascade');
//         $table->foreignId('end_ayah_id')->constrained('verses')->onDelete('cascade');
//         $table->integer('start_page')->nullable();
//         $table->integer('end_page')->nullable();
//         $table->integer('target_pages')->nullable();

        
//         $table->foreignId('actual_end_surah_id')->nullable()->constrained('surahs')->onDelete('cascade');
//         $table->foreignId('actual_end_ayah_id')->nullable()->constrained('verses')->onDelete('cascade');
//         $table->integer('actual_end_page')->nullable();
//         $table->integer('actual_pages')->nullable();
//         $table->decimal('achievement_percentage', 5, 2)->virtualAs('(actual_pages / IFNULL(target_pages, 1)) * 100');

        
//         $table->integer('tajweed_score')->nullable();
//         $table->integer('fluency_score')->nullable();
//         $table->integer('memory_score')->nullable();
//         $table->text('evaluation_notes')->nullable();
//         $table->text('notes')->nullable();

//         $table->timestamps();
//     });
// }