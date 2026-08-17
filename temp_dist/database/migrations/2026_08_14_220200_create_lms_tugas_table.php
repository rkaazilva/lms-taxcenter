<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_tugas', function (Blueprint $table) {
            $table->id();
            $table->string('id_tugas')->unique();
            $table->string('mapel');
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->text('link_soal')->nullable();
            $table->string('deadline')->nullable();
            $table->string('kelas')->nullable()->default('Semua');
            $table->boolean('blast')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_tugas');
    }
};
