<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mata_kuliah_id');
            $table->unsignedBigInteger('semester_id');
            $table->unsignedBigInteger('user_id');
            $table->json('dosen_pengampu_json')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('status')->default('draft');
            $table->string('version_label')->default('v0.1');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['mata_kuliah_id', 'semester_id']);
        });

        Schema::table('rps', function (Blueprint $table) {
            $table->foreign('mata_kuliah_id')->references('id')->on('mata_kuliah')->cascadeOnDelete();
            $table->foreign('semester_id')->references('id')->on('semesters')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('rps', function (Blueprint $table) {
            $table->dropForeign(['mata_kuliah_id']);
            $table->dropForeign(['semester_id']);
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('rps');
    }
};
