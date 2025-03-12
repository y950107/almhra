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
        Schema::create('surahs', function (Blueprint $table) {
            $table->id();
            $table->integer('surah_number')->unique(); // رقم السورة
            $table->string('name_arabic'); // اسم السورة بالعربية
            $table->string('name_english'); // اسم السورة بالإنجليزية
            $table->integer('ayah_count'); // عدد الآيات في السورة
            $table->string('revelation_type'); // نوع الوحي (مكي/مدني)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surahs');
    }
};
