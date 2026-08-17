<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('lms_users') && !Schema::hasColumn('lms_users', 'telepon')) {
            Schema::table('lms_users', function (Blueprint $table) {
                $table->string('telepon')->nullable()->after('kelas');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('lms_users') && Schema::hasColumn('lms_users', 'telepon')) {
            Schema::table('lms_users', function (Blueprint $table) {
                $table->dropColumn('telepon');
            });
        }
    }
};
