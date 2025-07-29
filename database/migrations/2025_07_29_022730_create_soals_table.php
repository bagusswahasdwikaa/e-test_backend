<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('soals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ujian_id');
            $table->text('pertanyaan')->nullable();
            $table->string('media_path')->nullable(); // untuk gambar/video
            $table->enum('media_type', ['image', 'video', 'none'])->default('none');
            $table->timestamps();

            // Foreign key ke tabel ujians
            $table->foreign('ujian_id')->references('id_ujian')->on('ujians')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('soals');
    }
};
