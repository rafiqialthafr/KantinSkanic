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
        // 1. Add bank/e-wallet account information to stands table
        Schema::table('stands', function (Blueprint $table) {
            if (! Schema::hasColumn('stands', 'bank_name')) {
                $table->string('bank_name', 50)->nullable()->after('no_wa');
            }
            if (! Schema::hasColumn('stands', 'nomor_rekening')) {
                $table->string('nomor_rekening', 50)->nullable()->after('bank_name');
            }
            if (! Schema::hasColumn('stands', 'atas_nama')) {
                $table->string('atas_nama', 100)->nullable()->after('nomor_rekening');
            }
        });

        // 2. Create settlements table to audit payout transfers from Admin to Vendors
        if (! Schema::hasTable('settlements')) {
            Schema::create('settlements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('stand_id')->constrained('stands')->cascadeOnDelete();
                $table->string('kode_settlement', 50)->unique();
                $table->unsignedInteger('total_dana');
                $table->enum('status', ['pending', 'ditransfer', 'settled', 'gagal'])->default('ditransfer');
                $table->text('catatan')->nullable();
                $table->string('bukti_transfer')->nullable();
                $table->timestamp('ditransfer_at')->nullable();
                $table->foreignId('ditransfer_oleh')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        // 3. Add payment method, status, batch grouping, and settlement tracking to orders table
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'batch_kode')) {
                $table->string('batch_kode', 50)->nullable()->after('kode_tr')->index();
            }
            if (! Schema::hasColumn('orders', 'metode_pembayaran')) {
                $table->enum('metode_pembayaran', ['qris', 'tunai'])->default('tunai')->after('total_harga');
            }
            if (! Schema::hasColumn('orders', 'status_pembayaran')) {
                $table->enum('status_pembayaran', ['belum_lunas', 'lunas'])->default('belum_lunas')->after('metode_pembayaran');
            }
            if (! Schema::hasColumn('orders', 'settlement_status')) {
                $table->enum('settlement_status', ['pending', 'settled'])->default('pending')->after('status_pembayaran');
            }
            if (! Schema::hasColumn('orders', 'settlement_id')) {
                $table->foreignId('settlement_id')->nullable()->after('settlement_status')->constrained('settlements')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'settlement_id')) {
                $table->dropForeign(['settlement_id']);
                $table->dropColumn('settlement_id');
            }
            if (Schema::hasColumn('orders', 'settlement_status')) {
                $table->dropColumn('settlement_status');
            }
            if (Schema::hasColumn('orders', 'status_pembayaran')) {
                $table->dropColumn('status_pembayaran');
            }
            if (Schema::hasColumn('orders', 'metode_pembayaran')) {
                $table->dropColumn('metode_pembayaran');
            }
            if (Schema::hasColumn('orders', 'batch_kode')) {
                $table->dropColumn('batch_kode');
            }
        });

        Schema::dropIfExists('settlements');

        Schema::table('stands', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('stands', 'atas_nama')) {
                $cols[] = 'atas_nama';
            }
            if (Schema::hasColumn('stands', 'nomor_rekening')) {
                $cols[] = 'nomor_rekening';
            }
            if (Schema::hasColumn('stands', 'bank_name')) {
                $cols[] = 'bank_name';
            }
            if (! empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
