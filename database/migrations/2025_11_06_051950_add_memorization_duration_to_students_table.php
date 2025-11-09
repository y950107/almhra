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
        Schema::table('students', function (Blueprint $table) {
            $table->smallInteger('maqraa_memorization_duration')->default(12); //مدة الحفظ قسم المقراة
            $table->smallInteger('mutqin_memorization_duration')->default(12); //مدة الحفظ قسم المتقن
            $table->smallInteger('mahir_memorization_duration')->default(12); //مدة الحفظ قسم التأسيس
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('maqraa_memorization_duration');
            $table->dropColumn('mutqin_memorization_duration');
            $table->dropColumn('mahir_memorization_duration');
        });
    }
};
