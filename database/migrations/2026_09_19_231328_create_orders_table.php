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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('kode_tr')->unique();
            $table->foreignId('stand_id')->constrained('stands')->cascadeOnDelete();
            $table->string('nama_pemesan');
            $table->string('kelas');
            $table->integer('total_harga');
            $table->enum('jam_pengambilan', ['Istirahat 1', 'Istirahat 2']);
            $table->enum('status', ['pending', 'diproses', 'siap_diambil', 'selesai', 'dibatalkan'])->default('pending');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
