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
        Schema::create('verses', function (Blueprint $table) {
            $table->id();
            $table->integer('ayah_number'); // رقم الآية داخل السورة
            $table->text('text'); // نص الآية
            $table->integer('page_number'); // رقم الصفحة في المصحف
            $table->foreignId('surah_id')->constrained('surahs')->onDelete('cascade'); // ربط الآية بالسورة
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verses');
    }
};
