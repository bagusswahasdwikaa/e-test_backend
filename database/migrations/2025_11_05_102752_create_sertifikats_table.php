<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sertifikats', function (Blueprint $table) {
            $table->id();
            // karena users.id = nvarchar, maka user_id harus string
            $table->string('user_id'); 
            $table->unsignedBigInteger('ujian_id');

            $table->string('nama_sertifikat');
            $table->string('path_file')->nullable();
            $table->dateTime('tanggal_diterbitkan');
            $table->timestamps();

            // buat foreign key manual tanpa tipe mismatch
            $table->foreign('ujian_id')
                ->references('id_ujian')
                ->on('ujians')
                ->onDelete('cascade');

            // untuk user_id, tidak bisa FK langsung karena beda tipe di SQL Server
            // jadi biarkan tanpa constraint FK (opsional)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sertifikats');
    }
};
