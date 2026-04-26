<?php

namespace App\Livewire;

use App\Mail\EnviarReciboMailable;
use App\Models\Cliente;
use App\Models\Marca;
use App\Models\Producto;
use App\Models\Recibo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

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
    public $listaClientesCacheada = []; // Cache de clientes para evitar recalcular en cada render

    #[Layout('layouts.app')]
    public function render()
    {
        $busquedaProducto = trim((string) $this->buscador);

        // obtener productos
        $productos = Producto::with('marcas')
            ->where(function ($query) use ($busquedaProducto) {
                if ($busquedaProducto === '') {
                    return;
                }

                $query->whereRaw('LOWER(nombre_producto) LIKE ?', ['%' . Str::lower($busquedaProducto) . '%'])
                    ->orWhere('id', 'like', '%' . $busquedaProducto . '%');
            })
            ->paginate(10);

        return view('livewire.ventas', [
            'productos' => $productos,
            'listaClientes' => $this->listaClientesCacheada
        ]);
    }

    /**
     * Busca clientes con debounce automático (Livewire lo maneja)
     * Se ejecuta solo cuando cambia busquedaCliente, no en cada render
     */
    #[\Livewire\Attributes\On('update:busquedaCliente')]
    public function actualizarListaClientes()
    {
        if ($this->tipoCliente == 'registrado') {
            $busquedaCliente = trim((string) $this->busquedaCliente);
            if ($busquedaCliente !== '') {
                $this->listaClientesCacheada = Cliente::where(function ($query) use ($busquedaCliente) {
                    $search = '%' . Str::lower($busquedaCliente) . '%';

                    $query->whereRaw('LOWER(nombres_cliente) LIKE ?', [$search])
                        ->orWhereRaw('LOWER(apellidos_cliente) LIKE ?', [$search])
                        ->orWhereRaw('LOWER(dui_cliente) LIKE ?', [$search]);
                })
                    ->take(5)
                    ->get();
            } else {
                $this->listaClientesCacheada = [];
            }
        }
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
            'marcaSeleccionada' => ['required', 'integer', Rule::exists('marca', 'id')],
            'cantidadAVender' => ['required', 'integer', 'min:1'],
        ], [
            'marcaSeleccionada.required' => 'Selecciona una marca.',
            'marcaSeleccionada.exists' => 'La marca seleccionada no es válida.',
            'cantidadAVender.required' => 'Ingresa la cantidad a vender.',
            'cantidadAVender.integer' => 'La cantidad debe ser un número entero.',
            'cantidadAVender.min' => 'La cantidad mínima es 1.',
        ]);

        if (!$this->productoSeleccionado) {
            $this->addError('marcaSeleccionada', 'Selecciona primero un producto válido.');
            return;
        }

        // Buscamos la información de la marca dentro de la relación del producto
        $marcaInfo = $this->productoSeleccionado->marcas
            ->where('id', $this->marcaSeleccionada)
            ->first();

        if (!$marcaInfo) {
            $this->addError('marcaSeleccionada', 'La marca seleccionada no pertenece al producto.');
            return;
        }

        if ($this->cantidadAVender > $marcaInfo->pivot->cantidad) {
            $this->addError('cantidadAVender', 'No puedes vender más de ' . $marcaInfo->pivot->cantidad . ' unidades.');
            return;
        }

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
        } else {
            $this->addError('carrito', 'Agrega al menos un producto al ticket.');
        }
    }
    public function guardarVenta()
    {
        $this->resetErrorBag();

        if (empty($this->carrito)) {
            $this->addError('carrito', 'Agrega al menos un producto al ticket.');
            return;
        }

        if ($this->totalVenta <= 0) {
            $this->addError('carrito', 'El total de la venta debe ser mayor a cero.');
            return;
        }

        if ($this->tipoCliente == 'registrado') {
            $this->validate([
                'clienteId' => ['required', 'integer', Rule::exists('cliente', 'id')],
            ], [
                'clienteId.required' => 'Seleccione un cliente registrado.',
                'clienteId.exists' => 'El cliente seleccionado no existe.',
            ]);

            if (!Cliente::find($this->clienteId)) {
                $this->addError('clienteId', 'El cliente seleccionado no existe.');
                return;
            }
        } else {
            $this->validate([
                'emailFacturacion' => ['required', 'email', 'max:255'],
            ], [
                'emailFacturacion.required' => 'Ingresa el correo para la factura electrónica.',
                'emailFacturacion.email' => 'El correo para la factura no tiene un formato válido.',
                'emailFacturacion.max' => 'El correo para la factura no debe superar 255 caracteres.',
            ]);
        }

        try {
            $idGenerado = DB::transaction(function () {
                $recibo = Recibo::create([
                    'fecha' => now()->format('Y-m-d'),
                    'total' => $this->totalVenta,
                    'id_cliente' => ($this->tipoCliente == 'registrado') ? $this->clienteId : null,
                    'email_invitado' => ($this->tipoCliente == 'invitado') ? $this->emailFacturacion : null,
                ]);

                $productosPorId = Producto::with('marcas')
                    ->whereIn('id', collect($this->carrito)->pluck('producto_id')->unique()->values())
                    ->get()
                    ->keyBy('id');

                foreach ($this->carrito as $item) {
                    $producto = $productosPorId->get($item['producto_id']);

                    if (!$producto) {
                        throw new \RuntimeException('Uno de los productos del ticket ya no existe.');
                    }

                    $marcaPivot = $producto->marcas()->where('marca_id', $item['marca_id'])->first();

                    if (!$marcaPivot) {
                        throw new \RuntimeException('Una de las marcas del ticket ya no está disponible.');
                    }

                    if ($marcaPivot->pivot->cantidad < $item['cantidad']) {
                        throw new \RuntimeException('No hay suficiente stock para ' . $producto->nombre_producto . '.');
                    }

                    $recibo->productos()->attach($item['producto_id'], [
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $item['precio'],
                    ]);

                    $producto->marcas()->updateExistingPivot($item['marca_id'], [
                        'cantidad' => $marcaPivot->pivot->cantidad - $item['cantidad'],
                        'venta_producto' => $marcaPivot->pivot->venta_producto + $item['cantidad'],
                    ]);

                    if ($this->tipoCliente == 'registrado') {
                        DB::table('cliente_producto')->insert([
                            'cliente_id' => $this->clienteId,
                            'producto_id' => $item['producto_id'],
                            'cantidad' => $item['cantidad'],
                            'created_at' => now(),
                        ]);
                    }
                }

                return $recibo->id;
            });

            $reciboParaEmail = Recibo::with('cliente')->find($idGenerado);
            $destinatario = null;

            if ($this->tipoCliente == 'registrado' && $reciboParaEmail && $reciboParaEmail->cliente) {
                $destinatario = $reciboParaEmail->cliente->email_cliente;
            } elseif ($this->tipoCliente == 'invitado' && $this->emailFacturacion) {
                $destinatario = $this->emailFacturacion;
            }

            if ($destinatario) {
                Mail::to($destinatario)->send(new EnviarReciboMailable($reciboParaEmail));
            }

            $this->reset(['carrito', 'totalVenta', 'clienteId', 'emailFacturacion', 'busquedaCliente', 'modalConfirmVenta']);
            $this->resetErrorBag();

            $this->dispatch('venta-realizada');
            $this->dispatch('abrir-ticket', id: $idGenerado);
        } catch (\Exception $e) {
            session()->flash('error', 'Ocurrió un error al procesar la venta: ' . $e->getMessage());
        }
    }

    public function updatedTipoCliente()
    {
        $this->reset(['clienteId', 'emailFacturacion']);
        $this->resetErrorBag();
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