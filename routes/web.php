<?php

use App\Livewire\Clientes\ListaClientes;
use App\Livewire\Clientes\VerClientes;
use App\Livewire\Configuracion\Ajustes;
use App\Livewire\Marcas\ListaMarcas;
use App\Livewire\Productos\ListaProductos;
use App\Livewire\Productos\VerProductos;
use App\Livewire\Ventas;
use App\Models\Cliente;
use App\Models\Recibo;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// --- INICIO ---
Route::get('/', function () {
    return view('welcome');
});

// --- DASHBOARD (Corregido para PostgreSQL) ---
Route::middleware(['auth'])->get('/dashboard', function () {
    $today = Carbon::today();
    $yearStart = $today->copy()->startOfYear();

    // Consulta de ventas mensuales corregida para Postgres
    $monthlySales = Recibo::selectRaw("EXTRACT(MONTH FROM fecha) as month, SUM(total) as total")
        ->whereBetween('fecha', [$yearStart, $today])
        ->groupBy('month')
        ->orderBy('month', 'asc')
        ->get();

    // Aquí deberías tener la lógica para llenar $metrics, $monthLabels, etc.
    // Por ahora defino lo básico para que no te de error de "variable indefinida"
    $recentVentas = Recibo::orderByDesc('created_at')
        ->orderByDesc('id')
        ->limit(5)
        ->get();

    return view('dashboard', [
        'recentVentas' => $recentVentas,
        'monthlySales' => $monthlySales,
        // Agrega aquí las demás variables que usa tu vista (metrics, monthLabels, etc.)
    ]);
})->name('dashboard');

// --- RECIBOS / PDF ---
Route::get('/recibo/{id}/pdf', function ($id) {
    $recibo = Recibo::with(['cliente', 'productos'])->findOrFail($id);
    $pdf = Pdf::loadView('pdf.recibo', compact('recibo'));
    $pdf->setPaper([0, 0, 226.77, 800], 'portrait');
    
    return $pdf->stream('recibo.pdf');
})->name('recibo.pdf');

// --- CLIENTES ---
Route::get('/clientes', ListaClientes::class)->name('lista-clientes');
Route::get('/clientes/{cliente}', VerClientes::class)->name('ver-cliente');

// --- MARCAS ---
Route::get('/marcas', ListaMarcas::class)->name('lista-marcas');

// --- INVENTARIO ---
Route::get('/kardex-inventario', ListaProductos::class)->name('lista-productos');
Route::get('/productos/{producto}', VerProductos::class)->name('ver-producto');

// --- VENTAS ---
Route::get('/ventas', Ventas::class)->name('ventas');

// --- CONFIGURACIÓN (Esta es la que causaba el error anterior) ---
Route::get('/configuracion', Ajustes::class)->name('configuracion');