<?php

namespace App\Providers;

use App\Models\Order;
use Illuminate\Pagination\Paginator;
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
        Paginator::useTailwind();

        View::composer(['vendor.*', 'dashboard.*'], function ($view) {
            if (Auth::check() && Auth::user()->stand) {
                $standId = Auth::user()->stand->id;
                $pending = Order::where('stand_id', $standId)->where('status', 'pending')->count();
                $siap = Order::where('stand_id', $standId)->where('status', 'siap_diambil')->count();
                $view->with([
                    'pendingOrdersCount' => $pending,
                    'siapDiambilOrdersCount' => $siap,
                    'activeOrdersCount' => $pending + $siap,
                ]);
            }
        });
    }
}
