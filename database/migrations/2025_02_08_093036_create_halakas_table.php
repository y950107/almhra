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
        Schema::create('halakas', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم الحلقة (مثلاً: برنامج تسميع الربع الأول)
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade'); // المعلم المشرف على الحلقة
            $table->date('start_date'); // تاريخ بدء الحلقة
            $table->boolean('halaka_status')->default(true);
            // يمكن إضافة أعمدة إضافية مثل وصف الحلقة أو حالة التسجيل
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('halaka');
    }
};
