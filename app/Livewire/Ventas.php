<?php

namespace App\Livewire;

use App\Models\Cliente;
use Livewire\Component;
use App\Models\Producto;
use App\Models\Marca;
use App\Models\Recibo;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

class Ventas extends Component
{
    public $buscador = '';
    public $modalSeleccion = false;
    public $modalConfirmVenta = false;

    public $carrito = [];
    public $totalVenta = 0;

    // productos
    public $productoId;
    public $productoSeleccionado;
    public $marcaSeleccionada;
    public $cantidadAVender = 1;
    public $stockMaximo = 0;

    // clientes
    public $tipoCliente = 'registrado';
    public $clienteId = null;
    public $emailFacturacion = '';
    public $busquedaCliente = '';

    #[Layout('layouts.app')]
    public function render()
    {
        // obtener productos
        $productos = Producto::with('marcas')
            ->where(DB::raw('LOWER(nombre_producto)'), 'like', '%' . strtolower($this->buscador) . '%')
            ->orWhere('id', 'like', '%' . $this->buscador . '%')
            ->paginate(10);

        //obtener clientes
        $listaClientes = [];
        if ($this->tipoCliente == 'registrado') {
            $listaClientes = Cliente::where('nombres_cliente', 'like', '%' . $this->busquedaCliente . '%')
                ->orWhere('apellidos_cliente', 'like', '%' . $this->busquedaCliente . '%')
                ->orWhere('dui_cliente', 'like', '%' . $this->busquedaCliente . '%')
                ->take(5)
                ->get();
        }

        return view('livewire.ventas', [
            'productos' => $productos,
            'listaClientes' => $listaClientes
        ]);
    }

    public function seleccionarProducto($id)
    {
        $this->productoId = $id;
        $this->productoSeleccionado = Producto::with('marcas')->find($id);
        $this->modalSeleccion = true;
    }

    public function updatedMarcaSeleccionada($value){
        if($value && $this->productoSeleccionado){
            $marca = $this->productoSeleccionado->marcas->where('id', $value)->first();

            if($marca){
                $this->stockMaximo = $marca->pivot->cantidad;

                $this->cantidadAVender = ($this->stockMaximo > 0) ? 1 : 0;
            }
        }else{
            $this->stockMaximo = 0;
            $this->cantidadAVender = 1;
        }
    }

    public function updatedCantidadAVender($value){
        if($value < 0 && $this->stockMaximo > 0 && $value){
            $this->cantidadAVender = 1;
        }
    }

    public function agregarAlCarrito()
    {
        $this->validate([
            'marcaSeleccionada' => 'required',
            'cantidadAVender' => 'required|integer|min:1',
        ]);

        // Buscamos la información de la marca dentro de la relación del producto
        $marcaInfo = $this->productoSeleccionado->marcas
            ->where('id', $this->marcaSeleccionada)
            ->first();

        $subtotal = $marcaInfo->pivot->precio_cliente * $this->cantidadAVender;

        // Agregamos al carrito (Array en memoria)
        $this->carrito[] = [
            'producto_id' => $this->productoId,
            'nombre'      => $this->productoSeleccionado->nombre_producto,
            'marca_id'    => $this->marcaSeleccionada,
            'marca'       => $marcaInfo->nombre_marca,
            'precio'      => $marcaInfo->pivot->precio_cliente,
            'cantidad'    => $this->cantidadAVender,
            'subtotal'    => $subtotal,
        ];

        $this->calcularTotal();
        $this->modalSeleccion = false;
        $this->reset(['marcaSeleccionada', 'cantidadAVender']);
    }

    public function quitarDelCarrito($index)
    {
        unset($this->carrito[$index]);
        $this->carrito = array_values($this->carrito);
        $this->calcularTotal();
    }

    public function calcularTotal()
    {
        $this->totalVenta = array_sum(array_column($this->carrito, 'subtotal'));
    }

    public function abrirConfirmacionVenta()
    {
        if (count($this->carrito) > 0) {
            $this->modalConfirmVenta = true;
        }
    }

    public function guardarVenta()
    {

        if ($this->tipoCliente == 'registrado' && !$this->clienteId) {
            $this->addError('busquedaCliente', 'Seleccione un cliente registrado.');
            return;
        }

        try {
            DB::transaction(function () {
                $recibo = Recibo::create([
                    'fecha'           => now()->format('Y-m-d'),
                    'total'           => $this->totalVenta,
                    'id_cliente'      => ($this->tipoCliente == 'registrado') ? $this->clienteId : null,
                    'email_invitado'  => ($this->tipoCliente == 'invitado') ? $this->emailFacturacion : null,
                ]);

                foreach ($this->carrito as $item) {
                    $recibo->productos()->attach($item['producto_id'], [
                        'cantidad' => $item['cantidad']
                    ]);

                    $producto = Producto::find($item['producto_id']);
                    $marcaPivot = $producto->marcas()->where('marca_id', $item['marca_id'])->first();

                    if ($marcaPivot) {
                        $producto->marcas()->updateExistingPivot($item['marca_id'], [
                            'cantidad'       => $marcaPivot->pivot->cantidad - $item['cantidad'],
                            'venta_producto' => $marcaPivot->pivot->venta_producto + $item['cantidad']
                        ]);
                    }

                    if ($this->tipoCliente == 'registrado') {
                        DB::table('cliente_producto')->insert([
                            'cliente_id'  => $this->clienteId,
                            'producto_id' => $item['producto_id'],
                            'cantidad'    => $item['cantidad'],
                            'created_at'  => now(),
                        ]);
                    }
                }
            });

            $this->reset(['carrito', 'totalVenta', 'clienteId', 'emailFacturacion', 'busquedaCliente', 'modalConfirmVenta']);
            $this->dispatch('venta-realizada');

        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }
    public function updatedTipoCliente()
    {
        $this->reset(['clienteId', 'emailFacturacion']);
    }
    public function cerrarModal(){
        $this->reset([
            'modalSeleccion',
            'productoId',
            'productoSeleccionado',
            'marcaSeleccionada',
            'cantidadAVender',
            'stockMaximo',
        ]);
    }
}