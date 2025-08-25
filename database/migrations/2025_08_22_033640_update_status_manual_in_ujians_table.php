<?php

// database/migrations/xxxx_xx_xx_xxxxxx_add_status_manual_to_ujians.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ujians', function (Blueprint $table) {
            $table->enum('status_manual', ['Aktif', 'Non Aktif'])->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('ujians', function (Blueprint $table) {
            $table->dropColumn('status_manual');
        });
    }
};
