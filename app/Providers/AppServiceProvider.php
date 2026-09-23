<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use App\Models\Configuracion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        // 1. Iniciamos un contador
        $queryCount = 0;

        // 2. Por cada consulta, sumamos 1
        DB::listen(function ($query) use (&$queryCount) {
            $queryCount++;
        });

        // 3. Justo antes de que Laravel termine de responder, guardamos el total en el log
        app()->terminating(function () use (&$queryCount) {
            // Solo nos alerta si hace más de 30 consultas (lo cual ya es sospechoso)
            if ($queryCount > 30) { 
                Log::warning("¡ALERTA DE RENDIMIENTO! Se ejecutaron {$queryCount} consultas a la BD en una sola petición.");
            }
        });
    }
}