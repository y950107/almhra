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

        /* Schema::table('evaluations', function (Blueprint $table) {
            
            $table->dropColumn('total_score');
        }); */
       /*  Schema::table('evaluations', function (Blueprint $table) {
            $table->integer('total_score')->storedAs('(tajweed_score + voice_score + memorization_score) / 3');
        }); */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            //$table->dropColumn('total_score');
            
        });
    }
};
