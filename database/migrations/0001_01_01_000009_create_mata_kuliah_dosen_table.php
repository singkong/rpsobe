<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mata_kuliah_dosen', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mata_kuliah_id');
            $table->unsignedBigInteger('dosen_id');
            $table->timestamps();

            $table->unique(['mata_kuliah_id', 'dosen_id']);
        });

        Schema::table('mata_kuliah_dosen', function (Blueprint $table) {
            $table->foreign('mata_kuliah_id')->references('id')->on('mata_kuliah')->cascadeOnDelete();
            $table->foreign('dosen_id')->references('id')->on('dosens')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('mata_kuliah_dosen', function (Blueprint $table) {
            $table->dropForeign(['mata_kuliah_id']);
            $table->dropForeign(['dosen_id']);
        });
        Schema::dropIfExists('mata_kuliah_dosen');
    }
};
