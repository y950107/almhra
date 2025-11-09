<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->enum('status', ['present', 'absent_with_excuse', 'absent_without_excuse']);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['student_id', 'date']); 
        });
    }
    public function down()
    {
        Schema::dropIfExists('attendances');
    }
};
