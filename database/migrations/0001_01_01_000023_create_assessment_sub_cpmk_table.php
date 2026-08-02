<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_sub_cpmk', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assessment_id');
            $table->unsignedBigInteger('sub_cpmk_id');
            $table->timestamps();

            $table->unique(['assessment_id', 'sub_cpmk_id']);
        });

        Schema::table('assessment_sub_cpmk', function (Blueprint $table) {
            $table->foreign('assessment_id')->references('id')->on('assessments')->cascadeOnDelete();
            $table->foreign('sub_cpmk_id')->references('id')->on('sub_cpmk')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('assessment_sub_cpmk', function (Blueprint $table) {
            $table->dropForeign(['assessment_id']);
            $table->dropForeign(['sub_cpmk_id']);
        });
        Schema::dropIfExists('assessment_sub_cpmk');
    }
};
