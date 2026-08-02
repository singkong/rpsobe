<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mata_kuliah', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kurikulum_id');
            $table->string('name');
            $table->string('code');
            $table->unsignedTinyInteger('sks')->default(3);
            $table->unsignedTinyInteger('semester');
            $table->string('jenis')->default('wajib');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['kurikulum_id', 'code']);
        });

        Schema::table('mata_kuliah', function (Blueprint $table) {
            $table->foreign('kurikulum_id')->references('id')->on('kurikulum')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('mata_kuliah', function (Blueprint $table) {
            $table->dropForeign(['kurikulum_id']);
        });
        Schema::dropIfExists('mata_kuliah');
    }
};
