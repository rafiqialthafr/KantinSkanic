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
        // 1. Parent Orders (Transaksi Utama terpusat untuk Midtrans Snap)
        if (! Schema::hasTable('parent_orders')) {
            Schema::create('parent_orders', function (Blueprint $table) {
                $table->id();
                $table->string('order_id', 64)->unique(); // ORDER-YYYYMMDD-XXXX
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('nama_pemesan', 100);
                $table->string('kelas', 50)->nullable();
                $table->unsignedInteger('gross_amount');
                $table->enum('payment_method', ['qris', 'cod'])->default('qris');
                $table->enum('payment_status', ['pending', 'paid', 'expired', 'failed'])->default('pending');
                $table->string('snap_token', 255)->nullable();
                $table->text('snap_redirect_url')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
            });
        }

        // 2. Link orders table as SubOrder per stand
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'parent_order_id')) {
                $table->foreignId('parent_order_id')->nullable()->after('id')->constrained('parent_orders')->nullOnDelete();
            }
            if (! Schema::hasColumn('orders', 'ticket_code')) {
                $table->string('ticket_code', 50)->nullable()->after('kode_tr')->index();
            }
        });

        // 3. Order Items notes
        Schema::table('order_items', function (Blueprint $table) {
            if (! Schema::hasColumn('order_items', 'notes')) {
                $table->text('notes')->nullable()->after('subtotal');
            }
        });

        // 4. Menu best seller & total sold
        Schema::table('menus', function (Blueprint $table) {
            if (! Schema::hasColumn('menus', 'is_best_seller')) {
                $table->boolean('is_best_seller')->default(false)->after('is_available');
            }
            if (! Schema::hasColumn('menus', 'total_sold')) {
                $table->unsignedInteger('total_sold')->default(0)->after('is_best_seller');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            if (Schema::hasColumn('menus', 'total_sold')) {
                $table->dropColumn('total_sold');
            }
            if (Schema::hasColumn('menus', 'is_best_seller')) {
                $table->dropColumn('is_best_seller');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'notes')) {
                $table->dropColumn('notes');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'ticket_code')) {
                $table->dropColumn('ticket_code');
            }
            if (Schema::hasColumn('orders', 'parent_order_id')) {
                $table->dropForeign(['parent_order_id']);
                $table->dropColumn('parent_order_id');
            }
        });

        Schema::dropIfExists('parent_orders');
    }
};
