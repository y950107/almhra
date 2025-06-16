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

            $table->dropForeign(['evaluator_id']);
            $table->dropColumn('evaluator_id');
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->onDelete('cascade');

            $table->integer('monthly_target_pages')->nullable()->change();
        });

        Schema::table('evaluations', function (Blueprint $table) {

            $table->dropForeign(['evaluator_id']);
            $table->dropColumn('evaluator_id');
            $table->foreignId('evaluator_id')->nullable()->constrained('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {

            $table->foreignId('evaluator_id')->nullable()->constrained('teachers')->onDelete('cascade');

            $table->dropForeign(['teacher_id']);
            $table->dropColumn('teacher_id');
            $table->decimal('monthly_target_pages',5,2)->nullable()->change();
        });

        Schema::table('evaluations', function (Blueprint $table) {

            $table->foreignId('evaluator_id')->nullable()->constrained('teachers')->onDelete('cascade');

            $table->dropForeign(['teacher_id']);
            $table->dropColumn('teacher_id');
        });
    }
};
