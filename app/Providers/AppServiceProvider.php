<?php

namespace App\Providers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(['vendor.*', 'dashboard.*'], function ($view) {
            if (Auth::check() && Auth::user()->stand) {
                $standId = Auth::user()->stand->id;
                $view->with([
                    'pendingOrdersCount' => Order::where('stand_id', $standId)->where('status', 'pending')->count(),
                    'siapDiambilOrdersCount' => Order::where('stand_id', $standId)->where('status', 'siap_diambil')->count(),
                ]);
            }
        });
    }
}
