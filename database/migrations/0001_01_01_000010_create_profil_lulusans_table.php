<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profil_lulusans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_studi_id');
            $table->string('name');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('profil_lulusans', function (Blueprint $table) {
            $table->foreign('program_studi_id')->references('id')->on('program_studi')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('profil_lulusans', function (Blueprint $table) {
            $table->dropForeign(['program_studi_id']);
        });
        Schema::dropIfExists('profil_lulusans');
    }
};
