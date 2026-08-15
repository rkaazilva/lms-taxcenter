<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('id_tugas');
            $table->string('email');
            $table->string('nama_siswa')->nullable();
            $table->text('link_tugas')->nullable();
            $table->integer('nilai')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->index(['id_tugas', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_submissions');
    }
};
