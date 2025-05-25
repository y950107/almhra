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
            $table->dropColumn('Progress_percentage');
            $table->dropColumn('achievement_percentage');

            $table->dropColumn('start_surah_id');
            $table->dropColumn('start_ayah_id');
            $table->dropColumn('end_surah_id');
            $table->dropColumn('end_ayah_id');
            $table->dropColumn('target_percentage');
            $table->dropColumn('start_page');
            $table->dropColumn('end_page');
            $table->dropColumn('target_lines');
            $table->dropColumn('target_pages');
            $table->dropColumn('actual_end_surah_id');
            $table->dropColumn('actual_end_ayah_id');
            $table->dropColumn('actual_end_page');
            $table->dropColumn('actuel_lines');
            $table->dropColumn('actual_pages');



            $table->string('student_evaluation')->nullable();


        });

        Schema::create('almaqraa_recitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recitation_session_id')->unique()->constrained('recitation_sessions')->onDelete('cascade');

            $table->integer('start_surah_id')->nullable();
            $table->integer('start_ayah_id')->nullable();

            $table->integer('end_surah_id')->nullable();
            $table->integer('end_ayah_id')->nullable();

            $table->integer('pages')->nullable();

            $table->timestamps();
        });

        Schema::create('almutqin_recitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recitation_session_id')->unique()->constrained('recitation_sessions')->onDelete('cascade');

            $table->integer('mem_start_surah_id')->nullable();
            $table->integer('mem_start_ayah_id')->nullable();
            $table->integer('mem_end_surah_id')->nullable();
            $table->integer('mem_end_ayah_id')->nullable();
            $table->integer('mem_pages')->nullable();

            $table->integer('rev_start_surah_id')->nullable();
            $table->integer('rev_start_ayah_id')->nullable();
            $table->integer('rev_end_surah_id')->nullable();
            $table->integer('rev_end_ayah_id')->nullable();
            $table->integer('rev_pages')->nullable();

            $table->string('lesson_title')->nullable();
            $table->string('lesson_type')->nullable();


            $table->timestamps();
        });

        Schema::create('almaher_recitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recitation_session_id')->unique()->constrained('recitation_sessions')->onDelete('cascade');
            $table->integer('start_surah_id')->nullable();
            $table->integer('start_ayah_id')->nullable();
            $table->integer('end_surah_id')->nullable();
            $table->integer('end_ayah_id')->nullable();
            $table->integer('pages')->nullable();

            $table->string('lesson_title')->nullable();
            $table->string('mem_lines')->nullable();
            $table->timestamps();
        });


    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the newly created tables
        Schema::dropIfExists('almaher_recitations');
        Schema::dropIfExists('almutqin_recitations');
        Schema::dropIfExists('almaqraa_recitations');

        // Add the dropped columns back to the recitation_sessions table
        Schema::table('recitation_sessions', function (Blueprint $table) {
            $table->integer('start_surah_id')->nullable();
            $table->integer('start_ayah_id')->nullable();
            $table->integer('end_surah_id')->nullable();
            $table->integer('end_ayah_id')->nullable();
            $table->decimal('target_percentage', 5, 2)->nullable();
            $table->integer('start_page')->nullable();
            $table->integer('end_page')->nullable();
            $table->integer('target_lines')->nullable();
            $table->integer('target_pages')->nullable();
            $table->integer('actual_end_surah_id')->nullable();
            $table->integer('actual_end_ayah_id')->nullable();
            $table->integer('actual_end_page')->nullable();
            $table->integer('actuel_lines')->nullable(); // Note: typo kept as in original
            $table->integer('actual_pages')->nullable();
            $table->decimal('achievement_percentage', 5, 2)->nullable();
            $table->decimal('Progress_percentage', 5, 2)->nullable(); // Note: PascalCase kept as in original

            $table->dropColumn('student_evaluation'); // Removing this as it was added in up()
        });
    }

};
