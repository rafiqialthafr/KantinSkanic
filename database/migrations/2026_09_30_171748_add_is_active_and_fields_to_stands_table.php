<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stands', function (Blueprint $table) {
            $table->string('pemilik')->nullable()->after('nama_stand');
            $table->string('no_wa', 20)->nullable()->after('pemilik');
            $table->boolean('is_active')->default(true)->after('deskripsi');
        });
    }

    public function down(): void
    {
        Schema::table('stands', function (Blueprint $table) {
            $table->dropColumn(['pemilik', 'no_wa', 'is_active']);
        });
    }
};
