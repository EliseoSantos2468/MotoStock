<?php

use App\Livewire\Clientes\ListaClientes;
use App\Livewire\Clientes\VerClientes;
use App\Livewire\Marcas\ListaMarcas;
use App\Livewire\Productos\ListaProductos;
use App\Livewire\Productos\VerProductos;
use App\Livewire\Ventas;
use App\Models\Recibo;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Recibos
Route::get('/recibo/{id}/pdf', function ($id){
    $recibo = Recibo::with(['cliente', 'productos'])->findOrFail($id);

    $pdf = Pdf::loadView('pdf.recibo', compact('recibo'));

    $pdf->setPaper([0, 0, 226.77, 800], 'portrait');

    return $pdf->stream("recibo-{$id}.pdf");
})->name("recibo.pdf");

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
    // ventas
    Route::get('/ventas', Ventas::class)->name('ventas');
});
