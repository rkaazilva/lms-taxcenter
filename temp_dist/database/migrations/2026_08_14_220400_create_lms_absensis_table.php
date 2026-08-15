<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_absensis', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('nama')->nullable();
            $table->string('mapel');
            $table->string('metode')->default('Live Zoom');
            $table->dateTime('timestamp')->nullable();
            $table->timestamps();

            $table->index(['email', 'mapel']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_absensis');
    }
};
