<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\Shift;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
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
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        View::composer('*', function ($view) {
            if (Auth::check()) {
                $currentShift = Shift::where('user_id', Auth::id())->where('status', 'open')->latest()->first();
                $lowStockBadgeCount = Product::whereColumn('current_stock', '<=', 'min_stock')->count();
                $view->with(compact('currentShift', 'lowStockBadgeCount'));
            }
        });
    }
}

