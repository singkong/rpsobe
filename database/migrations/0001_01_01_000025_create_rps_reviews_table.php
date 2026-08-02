<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rps_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rps_id');
            $table->unsignedBigInteger('reviewer_id');
            $table->unsignedTinyInteger('skor_total')->nullable();
            $table->json('skor_per_komponen')->nullable();
            $table->json('komentar')->nullable();
            $table->string('status')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        Schema::table('rps_reviews', function (Blueprint $table) {
            $table->foreign('rps_id')->references('id')->on('rps')->cascadeOnDelete();
            $table->foreign('reviewer_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('rps_reviews', function (Blueprint $table) {
            $table->dropForeign(['rps_id']);
            $table->dropForeign(['reviewer_id']);
        });
        Schema::dropIfExists('rps_reviews');
    }
};
