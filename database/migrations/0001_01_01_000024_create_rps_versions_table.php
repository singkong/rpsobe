<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rps_versions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rps_id');
            $table->string('version_label');
            $table->json('snapshot_data');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });

        Schema::table('rps_versions', function (Blueprint $table) {
            $table->foreign('rps_id')->references('id')->on('rps')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('rps_versions', function (Blueprint $table) {
            $table->dropForeign(['rps_id']);
            $table->dropForeign(['created_by']);
        });
        Schema::dropIfExists('rps_versions');
    }
};
