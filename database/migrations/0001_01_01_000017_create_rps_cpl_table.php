<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rps_cpl', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rps_id');
            $table->unsignedBigInteger('cpl_id');
            $table->timestamps();

            $table->unique(['rps_id', 'cpl_id']);
        });

        Schema::table('rps_cpl', function (Blueprint $table) {
            $table->foreign('rps_id')->references('id')->on('rps')->cascadeOnDelete();
            $table->foreign('cpl_id')->references('id')->on('cpls')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('rps_cpl', function (Blueprint $table) {
            $table->dropForeign(['rps_id']);
            $table->dropForeign(['cpl_id']);
        });
        Schema::dropIfExists('rps_cpl');
    }
};
