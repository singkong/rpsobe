<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rps_id');
            $table->string('nama');
            $table->decimal('bobot_persen', 5, 2)->default(0);
            $table->string('jenis')->default('formatif');
            $table->text('deskripsi')->nullable();
            $table->text('rubrik')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('assessments', function (Blueprint $table) {
            $table->foreign('rps_id')->references('id')->on('rps')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropForeign(['rps_id']);
        });
        Schema::dropIfExists('assessments');
    }
};
