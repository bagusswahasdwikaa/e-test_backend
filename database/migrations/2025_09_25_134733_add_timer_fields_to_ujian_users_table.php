<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ujian_users', function (Blueprint $table) {
            // waktu mulai ujian peserta
            $table->timestamp('started_at')->nullable()->after('submitted_at');
            // batas waktu selesai ujian peserta
            $table->timestamp('end_time')->nullable()->after('started_at');
        });
    }

    public function down(): void
    {
        Schema::table('ujian_users', function (Blueprint $table) {
            $table->dropColumn(['started_at', 'end_time']);
        });
    }
};
