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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stand_id')->constrained('stands')->cascadeOnDelete();
            $table->string('nama_menu');
            $table->enum('kategori', ['makanan', 'minuman', 'snack']);
            $table->integer('harga');
            $table->integer('stok')->default(0);
            $table->boolean('is_available')->default(true);
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
