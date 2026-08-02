<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cpml', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rps_id');
            $table->string('code');
            $table->text('deskripsi');
            $table->string('level_taksonomi')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('cpml', function (Blueprint $table) {
            $table->foreign('rps_id')->references('id')->on('rps')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('cpml', function (Blueprint $table) {
            $table->dropForeign(['rps_id']);
        });
        Schema::dropIfExists('cpml');
    }
};
