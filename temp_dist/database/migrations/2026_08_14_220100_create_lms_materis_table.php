<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_materis', function (Blueprint $table) {
            $table->id();
            $table->string('mapel');
            $table->string('judul');
            $table->text('link_modul')->nullable();
            $table->text('link_youtube')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('status')->default('Rilis');
            $table->string('kelas')->nullable()->default('Semua');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_materis');
    }
};
