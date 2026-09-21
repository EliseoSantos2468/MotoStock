<?php

namespace App\Livewire\Recepciones;

use App\Models\FacturaCompra;
use App\Models\FacturaCompraDetalle;
use App\Models\Marca;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

class FormRecepcion extends Component
{
    // Modo
    public $facturaCompra = null;
    public bool $modoEdicion = false;

    // Cabecera
    public string $numeroFactura = '';
    public string $fecha         = '';
    public string $proveedorId   = '';

    // Detalles en memoria (solo modo crear)
    public array $detalles = [];

    // Modal agregar detalle
    public bool   $modalAgregarDetalle      = false;
    public ?int   $agregarProductoId        = null;
    public ?int   $agregarMarcaId           = null;
    public int    $agregarCantidadEsperada  = 1;
    public string $agregarPrecioUnitario    = '';
    public array  $marcasDisponibles        = [];

    // Modal confirmar recepción
    public bool  $modalConfirmar      = false;
    public array $cantidadesRecibidas = [];

    protected $messages = [
        'numeroFactura.required'             => 'El número de factura es obligatorio.',
        'numeroFactura.max'                  => 'El número de factura no debe superar 100 caracteres.',
        'fecha.required'                     => 'La fecha es obligatoria.',
        'fecha.date'                         => 'La fecha no tiene un formato válido.',
        'proveedorId.required'               => 'Selecciona un proveedor.',
        'agregarProductoId.required'         => 'Selecciona un producto.',
        'agregarMarcaId.required'            => 'Selecciona una marca.',
        'agregarCantidadEsperada.required'   => 'Ingresa la cantidad esperada.',
        'agregarCantidadEsperada.min'        => 'La cantidad mínima es 1.',
        'agregarPrecioUnitario.required'     => 'Ingresa el precio unitario.',
        'agregarPrecioUnitario.min'          => 'El precio debe ser mayor a 0.',
    ];

    public function mount(?FacturaCompra $facturaCompra = null): void
    {
        if ($facturaCompra && $facturaCompra->exists) {
            $this->facturaCompra = $facturaCompra->load(['proveedor', 'detalles.producto', 'detalles.marca']);
            $this->modoEdicion   = true;
            $this->numeroFactura = $facturaCompra->numero_factura;
            $this->fecha         = $facturaCompra->fecha->format('Y-m-d');
            $this->proveedorId   = (string) $facturaCompra->proveedor_id;
        } else {
            $this->fecha = now()->format('Y-m-d');
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $proveedores = Proveedor::orderBy('nombre_proveedor')->get();
        $productos   = Producto::orderBy('nombre_producto')->get();

        return view('livewire.recepciones.form-recepcion', compact('proveedores', 'productos'));
    }

    // ─── Gestión de detalles (modo crear) ──────────────────────────────────

    public function updatedAgregarProductoId($value): void
    {
        $this->agregarMarcaId     = null;
        $this->marcasDisponibles  = [];

        if ($value) {
            $producto = Producto::with('marcas')->find($value);
            if ($producto) {
                $this->marcasDisponibles = $producto->marcas
                    ->map(fn($m) => ['id' => $m->id, 'nombre_marca' => $m->nombre_marca])
                    ->toArray();
            }
        }
    }

    public function abrirModalDetalle(): void
    {
        $this->resetValidation();
        $this->reset(['agregarProductoId', 'agregarMarcaId', 'agregarPrecioUnitario', 'marcasDisponibles']);
        $this->agregarCantidadEsperada = 1;
        $this->modalAgregarDetalle     = true;
    }

    public function agregarDetalle(): void
    {
        $this->validate([
            'agregarProductoId'       => ['required', 'integer', Rule::exists('producto', 'id')->where('user_id', Auth::id())],
            'agregarMarcaId'          => ['required', 'integer', Rule::exists('marca', 'id')->where('user_id', Auth::id())],
            'agregarCantidadEsperada' => ['required', 'integer', 'min:1'],
            'agregarPrecioUnitario'   => ['required', 'numeric', 'min:0.01'],
        ]);

        $producto = Producto::find($this->agregarProductoId);
        $marca    = Marca::find($this->agregarMarcaId);

        $this->detalles[] = [
            'producto_id'       => $this->agregarProductoId,
            'nombre_producto'   => $producto?->nombre_producto ?? '',
            'marca_id'          => $this->agregarMarcaId,
            'nombre_marca'      => $marca?->nombre_marca ?? '',
            'cantidad_esperada' => $this->agregarCantidadEsperada,
            'precio_unitario'   => (float) $this->agregarPrecioUnitario,
        ];

        $this->modalAgregarDetalle = false;
        $this->reset(['agregarProductoId', 'agregarMarcaId', 'agregarPrecioUnitario', 'marcasDisponibles']);
        $this->agregarCantidadEsperada = 1;
    }

    public function quitarDetalle(int $index): void
    {
        unset($this->detalles[$index]);
        $this->detalles = array_values($this->detalles);
    }

    // ─── Guardar nueva factura ──────────────────────────────────────────────

    public function guardarFactura(): void
    {
        $this->validate([
            'numeroFactura' => ['required', 'string', 'max:100'],
            'fecha'         => ['required', 'date'],
            'proveedorId'   => ['required', 'integer', Rule::exists('proveedores', 'id')->where('user_id', Auth::id())],
        ]);

        if (empty($this->detalles)) {
            $this->addError('detalles', 'Agrega al menos un producto a la factura.');
            return;
        }

        try {
            $factura = DB::transaction(function () {
                $factura = FacturaCompra::create([
                    'numero_factura' => $this->numeroFactura,
                    'fecha'          => $this->fecha,
                    'estado'         => 'pendiente',
                    'proveedor_id'   => $this->proveedorId,
                ]);

                foreach ($this->detalles as $item) {
                    FacturaCompraDetalle::create([
                        'factura_compra_id' => $factura->id,
                        'producto_id'       => $item['producto_id'],
                        'marca_id'          => $item['marca_id'],
                        'cantidad_esperada' => $item['cantidad_esperada'],
                        'cantidad_recibida' => 0,
                        'precio_unitario'   => $item['precio_unitario'],
                    ]);
                }

                return $factura;
            });

            $this->redirect(route('ver-recepcion', $factura->id), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    // ─── Confirmar recepción ────────────────────────────────────────────────

    public function abrirConfirmarRecepcion(): void
    {
        $this->cantidadesRecibidas = [];
        foreach ($this->facturaCompra->detalles as $detalle) {
            $this->cantidadesRecibidas[$detalle->id] = $detalle->cantidad_esperada;
        }
        $this->modalConfirmar = true;
    }

    public function confirmarRecepcion(): void
    {
        $rules    = [];
        $messages = [];

        foreach ($this->facturaCompra->detalles as $detalle) {
            $key              = "cantidadesRecibidas.{$detalle->id}";
            $rules[$key]      = ['required', 'integer', 'min:0', "max:{$detalle->cantidad_esperada}"];
            $messages["{$key}.required"] = 'Ingresa la cantidad recibida.';
            $messages["{$key}.min"]      = 'La cantidad no puede ser negativa.';
            $messages["{$key}.max"]      = "No puede superar {$detalle->cantidad_esperada} unidades esperadas.";
        }

        $this->validate($rules, $messages);

        try {
            DB::transaction(function () {
                foreach ($this->facturaCompra->detalles as $detalle) {
                    $nuevaCantidad = (int) ($this->cantidadesRecibidas[$detalle->id] ?? 0);
                    $incremento    = $nuevaCantidad - $detalle->cantidad_recibida;

                    $detalle->update(['cantidad_recibida' => $nuevaCantidad]);

                    if ($incremento > 0) {
                        $producto = Producto::with('marcas')->find($detalle->producto_id);
                        if ($producto) {
                            $marcaPivot = $producto->marcas()->where('marca_id', $detalle->marca_id)->first();
                            if ($marcaPivot) {
                                $producto->marcas()->updateExistingPivot($detalle->marca_id, [
                                    'cantidad'    => $marcaPivot->pivot->cantidad + $incremento,
                                    'precio_costo' => $detalle->precio_unitario,
                                ]);
                            }
                        }
                    }
                }

                // Recalcular estado de la factura
                $this->facturaCompra->refresh()->load('detalles');
                $totalEsperado = $this->facturaCompra->detalles->sum('cantidad_esperada');
                $totalRecibido = $this->facturaCompra->detalles->sum('cantidad_recibida');

                $nuevoEstado = 'pendiente';
                if ($totalRecibido >= $totalEsperado) {
                    $nuevoEstado = 'recibida';
                } elseif ($totalRecibido > 0) {
                    $nuevoEstado = 'parcial';
                }

                $this->facturaCompra->update(['estado' => $nuevoEstado]);
            });

            $this->facturaCompra->refresh()->load(['proveedor', 'detalles.producto', 'detalles.marca']);
            $this->modalConfirmar = false;
            $this->dispatch('recepcion-confirmada');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al confirmar la recepción: ' . $e->getMessage());
        }
    }
}
