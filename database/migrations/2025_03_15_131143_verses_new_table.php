<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Schema::create('verses_new', function (Blueprint $table) {
        //     $table->id();
        //     $table->integer('jozz');
        //     $table->integer('sura_no');
        //     $table->string('sura_name_en');
        //     $table->string('sura_name_ar');
        //     $table->integer('page');
        //     $table->integer('line_start');
        //     $table->integer('line_end');
        //     $table->integer('aya_no');
        //     $table->text('aya_text');
        //     $table->text('aya_text_emlaey');
        //     $table->timestamps();
        // });
    }

    public function down()
    {
       // Schema::dropIfExists('verses_new');
    }
};
