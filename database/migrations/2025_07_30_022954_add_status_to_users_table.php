<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tambahkan kolom `status` ke tabel users.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['aktif', 'non aktif'])->after('role');
        });
    }

    /**
     * Hapus kolom `status` dan semua constraint terkait di SQL Server.
     */
    public function down(): void
    {
        // 1. Hapus DEFAULT constraint pada kolom 'status'
        DB::statement("
            DECLARE @DefaultConstraintName NVARCHAR(128);
            SELECT @DefaultConstraintName = dc.name
            FROM sys.default_constraints dc
            INNER JOIN sys.columns c ON c.default_object_id = dc.object_id
            WHERE dc.parent_object_id = OBJECT_ID('users') AND c.name = 'status';

            IF @DefaultConstraintName IS NOT NULL
            BEGIN
                EXEC('ALTER TABLE users DROP CONSTRAINT [' + @DefaultConstraintName + ']');
            END
        ");

        // 2. Hapus CHECK constraint (jika ada, untuk enum)
        DB::statement("
            DECLARE @CheckConstraintName NVARCHAR(128);
            SELECT @CheckConstraintName = cc.name
            FROM sys.check_constraints cc
            INNER JOIN sys.columns col ON cc.parent_object_id = col.object_id AND cc.parent_column_id = col.column_id
            WHERE cc.parent_object_id = OBJECT_ID('users') AND col.name = 'status';

            IF @CheckConstraintName IS NOT NULL
            BEGIN
                EXEC('ALTER TABLE users DROP CONSTRAINT [' + @CheckConstraintName + ']');
            END
        ");

        // 3. Hapus kolom 'status'
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
