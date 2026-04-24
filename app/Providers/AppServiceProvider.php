<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Configuracion;

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
        $primary = "rgb(71 85 105)";
        $secondary = "#3b82f6";

        try {
            $config = Configuracion::first();
            
            if ($config) {
                $primary = $config->color_primario;
                $secondary = $config->color_secundario;
            }
        } catch (\Exception $e) {
        }
        View::share('primaryColor', $primary);
        View::share('secondaryColor', $secondary);
        
        View::share('white', '#ffffff');
        View::share('black', '#000000');
        View::share('btnEditar', '#f59e0b');
        View::share('btnEliminar', '#ef4444');
        View::share('btnVer', '#64748b');
    }
}