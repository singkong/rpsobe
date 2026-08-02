<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sub_cpmk', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cpml_id');
            $table->string('code');
            $table->text('deskripsi');
            $table->string('level_taksonomi')->nullable();
            $table->json('pertemuan_terkait')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('sub_cpmk', function (Blueprint $table) {
            $table->foreign('cpml_id')->references('id')->on('cpml')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sub_cpmk', function (Blueprint $table) {
            $table->dropForeign(['cpml_id']);
        });
        Schema::dropIfExists('sub_cpmk');
    }
};
