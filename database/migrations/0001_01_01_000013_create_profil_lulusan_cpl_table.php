<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profil_lulusan_cpl', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profil_lulusan_id');
            $table->unsignedBigInteger('cpl_id');
            $table->timestamps();

            $table->unique(['profil_lulusan_id', 'cpl_id']);
        });

        Schema::table('profil_lulusan_cpl', function (Blueprint $table) {
            $table->foreign('profil_lulusan_id')->references('id')->on('profil_lulusans')->cascadeOnDelete();
            $table->foreign('cpl_id')->references('id')->on('cpls')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('profil_lulusan_cpl', function (Blueprint $table) {
            $table->dropForeign(['profil_lulusan_id']);
            $table->dropForeign(['cpl_id']);
        });
        Schema::dropIfExists('profil_lulusan_cpl');
    }
};
