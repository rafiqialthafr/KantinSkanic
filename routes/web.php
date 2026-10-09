<?php

use App\Http\Controllers\Admin\MenuController as AdminMenuController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\StandController as AdminStandController;
use App\Http\Controllers\Admin\SystemController as AdminSystemController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\Vendor\MenuController as VendorMenuController;
use App\Http\Controllers\Vendor\OrderController as VendorOrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - KantinSkanic School Pre-Order System
|--------------------------------------------------------------------------
*/

// ==========================================================
// 1. PUBLIC — Katalog Siswa (accessible by anyone)
// ==========================================================
Route::get('/', [KatalogController::class, 'index'])->name('katalog.index');
Route::get('/api/menu-stocks', [KatalogController::class, 'menuStocks'])->name('katalog.stocks');
Route::get('/order/{kode_tr}', [KatalogController::class, 'orderStatus'])->name('order.status');
Route::get('/riwayat-pesanan', [KatalogController::class, 'orderHistory'])->name('order.history');
Route::post('/api/riwayat-pesanan', [KatalogController::class, 'orderHistoryApi'])->name('order.history.api');

// ==========================================================
// 2. CHECKOUT — requires auth (siswa/any role)
//    Guest cart is retained in LocalStorage; on checkout
//    press, guest is routed to /login (with intended /checkout)
// ==========================================================
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [KatalogController::class, 'showCheckout'])->name('checkout.show');
    Route::post('/checkout', [KatalogController::class, 'checkout'])->name('checkout');
});

// ==========================================================
// 3. AUTHENTICATION GATEWAY — Single Login/Register
// ==========================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Siswa quick register (retains intended URL for guest cart)
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    // ---- Lupa Kata Sandi ----
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ==========================================================
// 4. SUPER ADMIN PANEL — /admin/*
// ==========================================================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminStandController::class, 'index'])->name('dashboard');
    Route::get('/stands', [AdminStandController::class, 'manageIndex'])->name('stands.index');
    Route::get('/stands/create', [AdminStandController::class, 'create'])->name('stands.create');
    Route::post('/stands', [AdminStandController::class, 'store'])->name('stands.store');
    Route::get('/stands/{stand}/edit', [AdminStandController::class, 'edit'])->name('stands.edit');
    Route::put('/stands/{stand}', [AdminStandController::class, 'update'])->name('stands.update');
    Route::delete('/stands/{stand}', [AdminStandController::class, 'destroy'])->name('stands.destroy');
    Route::post('/stands/{stand}/toggle', [AdminStandController::class, 'toggleActive'])->name('stands.toggle');

    // Riwayat Transaksi (All Orders)
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');

    // Menu Management Overview
    Route::get('/menus', [AdminMenuController::class, 'index'])->name('menus.index');

    // Laporan & Omset
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');

    // Informasi Sistem & Operasional
    Route::get('/system', [AdminSystemController::class, 'index'])->name('system.index');

    // Reset vendor password
    Route::get('/vendors/{user}/reset-password', [AdminStandController::class, 'showResetPassword'])->name('vendors.resetPassword');
    Route::post('/vendors/{user}/reset-password', [AdminStandController::class, 'resetPassword'])->name('vendors.doResetPassword');
});

// ==========================================================
// 5. VENDOR / STAND PANEL — /vendor/*
// ==========================================================
Route::prefix('vendor')->name('vendor.')->middleware(['auth', 'role:penjual'])->group(function () {
    // Orders Dashboard (main landing after login)
    Route::get('/dashboard', [VendorOrderController::class, 'index'])->name('dashboard');
    Route::get('/omset', [VendorOrderController::class, 'omset'])->name('omset');
    Route::post('/orders/{order}/status', [VendorOrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::post('/orders/verify', [VendorOrderController::class, 'verify'])->name('orders.verify');

    // Menu Management
    Route::get('/menus', [VendorMenuController::class, 'index'])->name('menus.index');
    Route::post('/menus', [VendorMenuController::class, 'store'])->name('menus.store');
    Route::put('/menus/{menu}', [VendorMenuController::class, 'update'])->name('menus.update');
    Route::post('/menus/{menu}/toggle', [VendorMenuController::class, 'toggle'])->name('menus.toggle');
    Route::delete('/menus/{menu}', [VendorMenuController::class, 'destroy'])->name('menus.destroy');
});

// ==========================================================
// 6. LEGACY ROUTE ALIASES — keep old URLs working
//    (existing links still work while we migrate views)
// ==========================================================
Route::middleware('auth')->group(function () {
    // Old /dashboard -> redirect to role-appropriate panel
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        if ($user->isPenjual()) {
            return redirect()->route('vendor.dashboard');
        }

        return redirect()->route('katalog.index');
    })->name('dashboard');

    // Old menus routes -> redirect to vendor namespace
    Route::get('/dashboard/menus', fn () => redirect()->route('vendor.menus.index'))->name('menus.index');

    // Old order status update -> redirect to vendor
    Route::post('/orders/{order}/status', [VendorOrderController::class, 'updateStatus'])->name('orders.updateStatus');
});
