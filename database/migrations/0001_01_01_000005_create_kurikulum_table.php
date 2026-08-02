<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kurikulum', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_studi_id');
            $table->string('name');
            $table->unsignedSmallInteger('tahun_mulai');
            $table->unsignedSmallInteger('tahun_selesai');
            $table->unsignedTinyInteger('total_sks')->default(144);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('kurikulum', function (Blueprint $table) {
            $table->foreign('program_studi_id')->references('id')->on('program_studi')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('kurikulum', function (Blueprint $table) {
            $table->dropForeign(['program_studi_id']);
        });
        Schema::dropIfExists('kurikulum');
    }
};
