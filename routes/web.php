<?php

use App\Livewire\Clientes\ListaClientes;
use App\Livewire\Clientes\VerClientes;
use App\Livewire\Configuracion\Ajustes;
use App\Livewire\Marcas\ListaMarcas;
use App\Livewire\Productos\ListaProductos;
use App\Livewire\Productos\VerProductos;
use App\Exports\FacturaCompraExport;
use App\Livewire\Proveedores\ListaProveedores;
use App\Livewire\Recepciones\FormRecepcion;
use App\Livewire\Recepciones\ListaRecepciones;
use App\Livewire\Ventas;
use Maatwebsite\Excel\Facades\Excel;
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

// --- DASHBOARD (Optimizado con índices) ---
Route::middleware(['auth'])->get('/dashboard', function () {
    $today = Carbon::today();
    $monthStart = $today->copy()->startOfMonth();
    $prevMonthStart = $monthStart->copy()->subMonthNoOverflow()->startOfMonth();
    $prevMonthEnd = $monthStart->copy()->subDay();
    $yearStart = $today->copy()->startOfYear();

    $percentageDelta = static function (float|int $current, float|int $previous): float {
        if ((float) $previous === 0.0) {
            return (float) $current > 0 ? 100.0 : 0.0;
        }
        return (($current - $previous) / $previous) * 100;
    };

    // OPTIMIZADO: Usar índices compuestos para consultas de rango
    $ventasMes = (float) Recibo::whereBetween('fecha', [$monthStart->toDateString(), $today->toDateString()])->sum('total');
    $ventasMesAnterior = (float) Recibo::whereBetween('fecha', [$prevMonthStart->toDateString(), $prevMonthEnd->toDateString()])->sum('total');

    $ordenesMes = Recibo::whereBetween('fecha', [$monthStart->toDateString(), $today->toDateString()])->count();
    $ordenesMesAnterior = Recibo::whereBetween('fecha', [$prevMonthStart->toDateString(), $prevMonthEnd->toDateString()])->count();

    // OPTIMIZADO: Usar índice en created_at
    $clientesMes = Cliente::whereBetween('created_at', [$monthStart->startOfDay(), $today->copy()->endOfDay()])->count();
    $clientesMesAnterior = Cliente::whereBetween('created_at', [$prevMonthStart->startOfDay(), $prevMonthEnd->copy()->endOfDay()])->count();

    $clientesTotal = Cliente::count();

    $conversion = $clientesMes > 0 ? ($ordenesMes / $clientesMes) * 100 : 0;
    $conversionAnterior = $clientesMesAnterior > 0 ? ($ordenesMesAnterior / $clientesMesAnterior) * 100 : 0;

    $metrics = [
        'ventas_mes' => $ventasMes,
        'ventas_delta' => $percentageDelta($ventasMes, $ventasMesAnterior),
        'ordenes_mes' => $ordenesMes,
        'ordenes_delta' => $percentageDelta($ordenesMes, $ordenesMesAnterior),
        'clientes_mes' => $clientesMes,
        'clientes_total' => $clientesTotal,
        'clientes_delta' => $percentageDelta($clientesMes, $clientesMesAnterior),
        'conversion' => $conversion,
        'conversion_delta' => $percentageDelta($conversion, $conversionAnterior),
    ];

    // OPTIMIZADO: Consulta mensual con índice en fecha
    $monthlySales = Recibo::selectRaw("EXTRACT(MONTH FROM fecha) as month, SUM(total) as total")
        ->whereBetween('fecha', [$yearStart->toDateString(), $today->toDateString()])
        ->groupBy('month')
        ->orderBy('month', 'asc')
        ->get();

    $monthLabels = collect(range(1, 12))
        ->map(fn (int $month): string => Carbon::create($today->year, $month, 1)->locale('es')->translatedFormat('M'))
        ->values();

    $monthlyTotals = array_fill(0, 12, 0.0);
    foreach ($monthlySales as $item) {
        $index = ((int) $item->month) - 1;
        if ($index >= 0 && $index < 12) {
            $monthlyTotals[$index] = (float) $item->total;
        }
    }

    // OPTIMIZADO: Top productos con índices en producto_recibo
    $topProductos = DB::table('producto_recibo as pr')
        ->join('producto as p', 'p.id', '=', 'pr.producto_id')
        ->select('p.nombre_producto', DB::raw('SUM(pr.cantidad) as total_cantidad'))
        ->groupBy('p.id', 'p.nombre_producto')
        ->orderByDesc('total_cantidad')
        ->limit(5)
        ->get();

    $topProductoLabels = $topProductos->pluck('nombre_producto')->values();
    $topProductoData = $topProductos->pluck('total_cantidad')->map(fn ($value) => (int) $value)->values();

    // OPTIMIZADO: Consultas de tendencias con índices
    $trendSeries = [];
    foreach ([7, 30, 90] as $range) {
        $rangeStart = $today->copy()->subDays($range - 1);

        $rows = Recibo::selectRaw('DATE(fecha) as dia, SUM(total) as total')
            ->whereBetween('fecha', [$rangeStart->toDateString(), $today->toDateString()])
            ->groupBy('dia')
            ->orderBy('dia')
            ->get()
            ->keyBy('dia');

        $labels = [];
        $data = [];

        for ($cursor = $rangeStart->copy(); $cursor->lte($today); $cursor->addDay()) {
            $key = $cursor->toDateString();
            $labels[] = $cursor->format('d/m');
            $data[] = isset($rows[$key]) ? (float) $rows[$key]->total : 0.0;
        }

        $trendSeries[(string) $range] = [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    // OPTIMIZADO: Ventas recientes con eager loading
    $recentVentas = Recibo::with(['cliente:id,nombres_cliente,apellidos_cliente'])
        ->orderByDesc('created_at')
        ->orderByDesc('id')
        ->limit(5)
        ->get();

    return view('dashboard', [
        'metrics' => $metrics,
        'recentVentas' => $recentVentas,
        'monthlySales' => $monthlySales,
        'monthLabels' => $monthLabels,
        'monthlyTotals' => $monthlyTotals,
        'topProductoLabels' => $topProductoLabels,
        'topProductoData' => $topProductoData,
        'trendSeries' => $trendSeries,
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

// --- PROVEEDORES ---
Route::get('/proveedores', ListaProveedores::class)->name('lista-proveedores');

// --- RECEPCIÓN DE MERCANCÍA ---
Route::get('/recepciones', ListaRecepciones::class)->name('lista-recepciones');
Route::get('/recepciones/nueva', FormRecepcion::class)->name('nueva-recepcion');
Route::get('/recepciones/{facturaCompra}', FormRecepcion::class)->name('ver-recepcion');
Route::get('/recepciones/{facturaCompra}/excel', function (\App\Models\FacturaCompra $facturaCompra) {
    $nombre = 'factura-' . str_replace(['/', '\\', ' '], '-', $facturaCompra->numero_factura) . '.xlsx';
    return Excel::download(new FacturaCompraExport($facturaCompra->load(['proveedor', 'detalles.producto', 'detalles.marca'])), $nombre);
})->middleware(['auth'])->name('recepcion.excel');

Route::get('/recepciones/{facturaCompra}/pdf', function (\App\Models\FacturaCompra $facturaCompra) {
    $factura = $facturaCompra->load(['proveedor', 'detalles.producto', 'detalles.marca']);
    $nombre  = 'factura-' . str_replace(['/', '\\', ' '], '-', $factura->numero_factura) . '.pdf';
    return Pdf::loadView('pdf.factura-compra', compact('factura'))
        ->setPaper('a4', 'landscape')
        ->stream($nombre);
})->middleware(['auth'])->name('recepcion.pdf');

// --- CONFIGURACIÓN (Esta es la que causaba el error anterior) ---
Route::get('/configuracion', Ajustes::class)->name('configuracion');