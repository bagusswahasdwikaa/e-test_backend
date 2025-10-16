<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ujians', function (Blueprint $table) {
            $table->enum('jenis_ujian', ['PRETEST', 'POSTEST'])->after('status');
            $table->integer('standar_minimal_nilai')->nullable()->after('jenis_ujian');
        });
    }

    public function down(): void
    {
        Schema::table('ujians', function (Blueprint $table) {
            $table->dropColumn(['jenis_ujian', 'standar_minimal_nilai']);
        });
    }
};
