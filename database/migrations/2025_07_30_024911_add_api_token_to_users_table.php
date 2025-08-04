<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('api_token', 80)->after('password')->nullable();
            // Jangan buat unique index disini, buat manual setelah ini
        });

        // Buat unique index yang hanya untuk api_token NOT NULL (filtered index di SQL Server)
        DB::statement('CREATE UNIQUE INDEX users_api_token_unique ON users(api_token) WHERE api_token IS NOT NULL;');
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::statement('DROP INDEX users_api_token_unique ON users');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('api_token');
        });
    }
};
