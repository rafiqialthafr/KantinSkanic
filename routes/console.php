<?php

use App\Http\Controllers\Api\OrderCheckoutController;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('orders:cleanup-expired', function () {
    $count = OrderCheckoutController::cleanupExpiredUnpaidOrders();
    $this->info("Berhasil membersihkan {$count} pesanan QRIS yang kedaluwarsa (> 24 jam).");
})->purpose('Bersihkan pesanan QRIS belum bayar yang telah melebihi batas 24 jam');

Schedule::command('orders:cleanup-expired')->everyTenMinutes();
