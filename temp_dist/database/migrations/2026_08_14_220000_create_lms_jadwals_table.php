<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_jadwals', function (Blueprint $table) {
            $table->id();
            $table->string('tanggal')->nullable();
            $table->string('jam')->nullable();
            $table->string('mapel');
            $table->string('materi');
            $table->string('dosen');
            $table->text('link_zoom')->nullable();
            $table->string('status_sesi')->default('AKAN_DATANG');
            $table->boolean('blast')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_jadwals');
    }
};
