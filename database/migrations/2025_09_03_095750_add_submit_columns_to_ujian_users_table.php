<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('ujian_users', function (Blueprint $table) {
            $table->boolean('is_submitted')->default(false);
            $table->timestamp('submitted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ujian_users', function (Blueprint $table) {
            //
        });
    }
};
