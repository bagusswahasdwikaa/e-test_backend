<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHasilUjianUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hasil_ujian_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ujian_user_id')->index();
            $table->string('nama_ujian');
            $table->timestamp('waktu_ujian_selesai')->nullable(); // Diambil dari submitted_at
            $table->integer('nilai')->nullable(); // Diambil dari nilai ujian_users
            $table->enum('status', ['Sudah Dikerjakan', 'Belum Dikerjakan']);
            $table->timestamps();

            // Foreign key ke tabel ujian_users
            $table->foreign('ujian_user_id')
                  ->references('id')
                  ->on('ujian_users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_ujian_users');
    }
}
