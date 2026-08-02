<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_studi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fakultas_id');
            $table->string('name');
            $table->string('code');
            $table->string('jenjang');
            $table->string('akreditasi')->nullable();
            $table->string('kaprodi_name')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('program_studi', function (Blueprint $table) {
            $table->foreign('fakultas_id')->references('id')->on('fakultas')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('program_studi', function (Blueprint $table) {
            $table->dropForeign(['fakultas_id']);
        });
        Schema::dropIfExists('program_studi');
    }
};
