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
        Schema::table('almaqraa_recitations', function (Blueprint $table) {
            $table->decimal('pages',5,1)->nullable()->change();
        });

        Schema::table('almutqin_recitations', function (Blueprint $table) {
            $table->decimal('mem_pages',5,1)->nullable()->change();
            $table->decimal('rev_pages',5,1)->nullable()->change();
        });

        Schema::table('almaher_recitations', function (Blueprint $table) {
            $table->decimal('pages',5,1)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('almaqraa_recitations', function (Blueprint $table) {
            $table->integer('pages')->nullable()->change();
        });

        Schema::table('almutqin_recitations', function (Blueprint $table) {
            $table->integer('mem_pages')->nullable()->change();
            $table->integer('rev_pages')->nullable()->change();
        });

        Schema::table('almaher_recitations', function (Blueprint $table) {
            $table->integer('pages')->nullable()->change();
        });
    }
};
