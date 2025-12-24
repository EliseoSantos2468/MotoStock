<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        View::share('primaryColor', "rgb(71 85 105)");
        View::share('secondaryColor', "#3b82f6");
        View::share('white', '#ffffff');
        View::share('black', '#000000');
        View::share('btnEditar', '#f59e0b');
        View::share('btnEliminar', '#ef4444');
        View::share('btnVer', '#64748b');
    }
}
