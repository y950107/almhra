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
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->date('birthdate');
            $table->string('qualification');
            $table->enum('quran_level', ['beginner', 'intermediate', 'advanced']);
            $table->boolean('has_ijaza')->default(false);
            $table->json('ijaza_types')->nullable();
            $table->json('desired_recitations');
            $table->integer('self_evaluation');
            $table->foreignId('teacher_id')->nullable()->constrained();
            $table->string('qualification_file');
            $table->string('audio_recitation');
            $table->enum('status', ['pending', 'accepted', 'rejected', 'interview'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
