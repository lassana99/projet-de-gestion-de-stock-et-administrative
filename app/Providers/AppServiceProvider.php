<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Invoice; // Import du Modèle Invoice
use App\Observers\InvoiceObserver; // Import de l'Observer
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

        // Code existant
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $cartCount = Cart::where('user_id', Auth::id())->count();
            } else {
                $cartCount = 0;
            }
            $view->with('cartCount', $cartCount);
        });

        Paginator::useBootstrap();
    }
}