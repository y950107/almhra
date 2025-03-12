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
        Schema::table('posts', function (Blueprint $table) {
            $table->string('slug');
            $table->text('body')->nullable();
            $table->string('photo_alt_text')->nullable();
            $table->boolean('is_visible')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('slug');
            $table->dropColumn('body');
            $table->dropColumn('photo_alt_text');
            $table->dropColumn('is_visible');
        });
    }
};
