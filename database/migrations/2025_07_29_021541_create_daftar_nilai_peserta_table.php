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
        Schema::create('daftar_nilai_peserta', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // relasi ke users.id
            $table->unsignedBigInteger('ujian_id');
            $table->date('tanggal');
            $table->float('nilai')->nullable();
            $table->enum('status', ['Selesai', 'Belum Dikerjakan'])->default('Belum Dikerjakan');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('ujian_id')->references('id_ujian')->on('ujians')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daftar_nilai_peserta');
    }
};
