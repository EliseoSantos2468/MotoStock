<?php

use App\Livewire\Clientes\ListaClientes;
use App\Livewire\Clientes\VerClientes;
use App\Livewire\Marcas\ListaMarcas;
use App\Livewire\Productos\ListaProductos;
use App\Livewire\Productos\VerProductos;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    // clientes
    Route::get('/clientes', ListaClientes::class)->name('lista-clientes');
    Route::get('/clientes/{cliente}', VerClientes::class)->name('ver-cliente');
    // marcas
    Route::get('/marcas', ListaMarcas::class)->name('lista-marcas');
    // inventario
    Route::get('/kardex-inventario', ListaProductos::class)->name('lista-productos');
    Route::get('/productos/{producto}', VerProductos::class)->name('ver-producto');
});
