<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cpls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_studi_id');
            $table->string('code');
            $table->text('deskripsi');
            $table->string('kategori');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['program_studi_id', 'code']);
        });

        Schema::table('cpls', function (Blueprint $table) {
            $table->foreign('program_studi_id')->references('id')->on('program_studi')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('cpls', function (Blueprint $table) {
            $table->dropForeign(['program_studi_id']);
        });
        Schema::dropIfExists('cpls');
    }
};
