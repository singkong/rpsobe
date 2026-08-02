<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materi_pertemuan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rps_id');
            $table->unsignedTinyInteger('pertemuan_ke');
            $table->unsignedBigInteger('sub_cpmk_id')->nullable();
            $table->text('materi');
            $table->text('indikator')->nullable();
            $table->json('referensi_ids')->nullable();
            $table->json('metode_pembelajaran')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('materi_pertemuan', function (Blueprint $table) {
            $table->foreign('rps_id')->references('id')->on('rps')->cascadeOnDelete();
            $table->foreign('sub_cpmk_id')->references('id')->on('sub_cpmk')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('materi_pertemuan', function (Blueprint $table) {
            $table->dropForeign(['rps_id']);
            $table->dropForeign(['sub_cpmk_id']);
        });
        Schema::dropIfExists('materi_pertemuan');
    }
};
