<?php

namespace App\Providers;

use App\Models\Category;
use App\Support\Cart;
use App\Support\Currency;
use Illuminate\Pagination\Paginator;
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
        Paginator::defaultView('partials.pagination');
        Paginator::defaultSimpleView('partials.pagination');

        View::composer('layouts.app', function ($view) {
            $view->with([
                'menuCategories' => Category::active()->where('show_in_menu', true)->get(),
                'cartCount' => Cart::count(),
                'cartTotal' => money(Cart::subtotal()),
                'currentCurrency' => Currency::current(),
            ]);
        });
    }
}
