<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cpml_cpl', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cpml_id');
            $table->unsignedBigInteger('cpl_id');
            $table->timestamps();

            $table->unique(['cpml_id', 'cpl_id']);
        });

        Schema::table('cpml_cpl', function (Blueprint $table) {
            $table->foreign('cpml_id')->references('id')->on('cpml')->cascadeOnDelete();
            $table->foreign('cpl_id')->references('id')->on('cpls')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('cpml_cpl', function (Blueprint $table) {
            $table->dropForeign(['cpml_id']);
            $table->dropForeign(['cpl_id']);
        });
        Schema::dropIfExists('cpml_cpl');
    }
};
