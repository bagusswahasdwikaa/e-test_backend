<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ujians', function (Blueprint $table) {
            $table->id('id_ujian');
            $table->string('nama_ujian');
            $table->date('tanggal');
            $table->integer('durasi'); // menit
            $table->integer('jumlah_soal');
            $table->float('nilai')->nullable()->default(100);
            $table->string('kode_soal');
            $table->enum('status', ['Aktif', 'Non Aktif'])->default('Non Aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ujians');
    }
};
