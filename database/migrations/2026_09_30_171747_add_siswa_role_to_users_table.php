<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add 'siswa' to the role enum and add stand_id FK.
     * We rebuild the column because SQLite/MySQL handle enum differently.
     */
    public function up(): void
    {
        // Re-create column with expanded enum (MySQL-compatible)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','penjual','siswa') NOT NULL DEFAULT 'siswa'");
        }

        Schema::table('users', function (Blueprint $table) {
            // Nullable stand_id so vendor users can be quickly looked up by stand
            $table->unsignedBigInteger('stand_id')->nullable()->after('role');
            $table->foreign('stand_id')->references('id')->on('stands')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['stand_id']);
            $table->dropColumn('stand_id');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','penjual') NOT NULL DEFAULT 'penjual'");
        }
    }
};
