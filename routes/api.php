<?php

use App\Http\Controllers\Api\LibroController;
use App\Http\Controllers\Api\MotoController;
use App\Http\Controllers\Api\UsuarioVentasController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'verificarRol:admin_motos,ventas'])->prefix('motos')->group(function (): void {
            Route::get('/', [MotoController::class, 'index'])->middleware('verificarRol:admin_motos,ventas');
            Route::get('/stock', [MotoController::class, 'stockReport'])->middleware('verificarRol:admin_motos,ventas');
            Route::get('/{id}', [MotoController::class, 'show'])->whereNumber('id')->middleware('verificarRol:admin_motos,ventas');
            Route::post('/venta', [MotoController::class, 'venta'])->middleware('verificarRol:admin_motos,ventas');
            Route::post('/', [MotoController::class, 'store'])->middleware('verificarRol:admin_motos');
            Route::put('/{id}', [MotoController::class, 'update'])->whereNumber('id')->middleware('verificarRol:admin_motos');
            Route::delete('/{id}', [MotoController::class, 'destroy'])->whereNumber('id')->middleware('verificarRol:admin_motos');
});

Route::middleware(['auth:sanctum', 'verificarRol:admin_motos'])->prefix('usuarios')->group(function (): void {
    Route::post('/ventas', [UsuarioVentasController::class, 'store']);
});

Route::middleware(['auth:sanctum', 'verificarRol:admin_libreria'])->prefix('libros')->group(function (): void {
    Route::get('/', [LibroController::class, 'index']);
    Route::get('/stock', [LibroController::class, 'stockReport']);
    Route::get('/{id}', [LibroController::class, 'show'])->whereNumber('id');
    Route::post('/prestamo', [LibroController::class, 'prestamo']);
    Route::put('/prestamo/{id}/devolucion', [LibroController::class, 'devolucion'])->whereNumber('id');
    Route::post('/', [LibroController::class, 'store']);
    Route::put('/{id}', [LibroController::class, 'update'])->whereNumber('id');
    Route::delete('/{id}', [LibroController::class, 'destroy'])->whereNumber('id');
});
