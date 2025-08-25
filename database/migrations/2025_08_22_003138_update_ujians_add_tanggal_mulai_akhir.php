<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ujians', function (Blueprint $table) {
            // Jika masih ada kolom "tanggal", hapus dulu
            if (Schema::hasColumn('ujians', 'tanggal')) {
                $table->dropColumn('tanggal');
            }

            // Tambah kolom baru
            $table->dateTime('tanggal_mulai')->after('nama_ujian');
            $table->dateTime('tanggal_akhir')->after('tanggal_mulai');
        });
    }

    public function down(): void
    {
        Schema::table('ujians', function (Blueprint $table) {
            // Kembalikan kolom lama "tanggal"
            $table->dateTime('tanggal')->nullable()->after('nama_ujian');

            // Hapus kolom baru
            $table->dropColumn(['tanggal_mulai', 'tanggal_akhir']);
        });
    }
};
