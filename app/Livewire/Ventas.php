<?php

namespace App\Livewire;

use App\Mail\EnviarReciboMailable;
use App\Models\Cliente;
use App\Models\Marca;
use App\Models\Producto;
use App\Models\Recibo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
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
    // estado de validación para la cantidad
    public $cantidadError = null;
    public $puedeAgregar = true;

    // clientes
    public $tipoCliente = 'registrado';
    // Tipo de precio: 'cliente' (público), 'taller' o 'mayoreo'.
    // $tipoPrecio es el predeterminado de la venta; cada línea del ticket
    // guarda el suyo y se puede cambiar individualmente.
    public $tipoPrecio = 'cliente';
    public $tipoPrecioItem = 'cliente';
    // Precio escrito a mano por el vendedor (tipo 'manual')
    public $precioManual = null;
    // Se llena cuando el precio manual está por debajo del mínimo configurado,
    // para pedir confirmación antes de agregarlo al ticket.
    public $alertaPrecioBajo = null;
    public $clienteId = null;
    public $clienteSeleccionadoNombre = null;
    public $clienteSeleccionadoDui = null;
    public $nombreInvitado = '';
    public $emailFacturacion = '';
    public $busquedaCliente = '';
    public $listaClientesCacheada = []; // Cache de clientes para evitar recalcular en cada render

    public const TIPOS_PRECIO = [
        'cliente' => 'Precio Cliente',
        'taller'  => 'Precio Taller',
        'mayoreo' => 'Precio Mayoreo',
        'manual'  => 'Precio Manual',
    ];

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
    public function updatedBusquedaCliente($value)
    {
        // Si el vendedor vuelve a escribir, se descarta el cliente elegido.
        $this->clienteId = null;
        $this->clienteSeleccionadoNombre = null;
        $this->clienteSeleccionadoDui = null;

        if ($this->tipoCliente == 'registrado') {
            $busquedaCliente = trim((string) $value);
            if ($busquedaCliente !== '') {
                $this->listaClientesCacheada = Cliente::where(function ($query) use ($busquedaCliente) {
                    $search = '%' . Str::lower($busquedaCliente) . '%';

                    $query->whereRaw('LOWER(nombres_cliente) LIKE ?', [$search])
                        ->orWhereRaw('LOWER(apellidos_cliente) LIKE ?', [$search])
                        ->orWhereRaw('LOWER(dui_cliente) LIKE ?', [$search]);
                })
                    ->where('user_id', Auth::id())
                    ->take(5)
                    ->get();
            } else {
                $this->listaClientesCacheada = [];
            }
        }
    }

    public function seleccionarCliente($id)
    {
        $cliente = Cliente::where('user_id', Auth::id())->find($id);

        if (!$cliente) {
            $this->addError('clienteId', 'El cliente seleccionado no existe.');
            return;
        }

        $this->clienteId = $cliente->id;
        $this->clienteSeleccionadoNombre = trim($cliente->nombres_cliente . ' ' . $cliente->apellidos_cliente);
        $this->clienteSeleccionadoDui = $cliente->dui_cliente;
        $this->busquedaCliente = '';
        $this->listaClientesCacheada = [];
        $this->resetErrorBag('clienteId');
    }

    public function quitarCliente()
    {
        $this->reset(['clienteId', 'clienteSeleccionadoNombre', 'clienteSeleccionadoDui', 'busquedaCliente']);
        $this->listaClientesCacheada = [];
    }

    public function seleccionarProducto($id)
    {
        $producto = Producto::with('marcas')->find($id);

        if (!$producto) {
            session()->flash('error', 'Este producto ya no está disponible. Actualiza la lista e inténtalo de nuevo.');
            return;
        }

        $this->productoId = $id;
        $this->productoSeleccionado = $producto;
        $this->tipoPrecioItem = $this->tipoPrecio;
        $this->precioManual = null;
        $this->alertaPrecioBajo = null;
        $this->modalSeleccion = true;
    }

    public function updatedMarcaSeleccionada($value)
    {
        if ($value && $this->productoSeleccionado) {
            $marca = $this->productoSeleccionado->marcas->where('id', $value)->first();

            if ($marca) {
                $this->stockMaximo = $marca->pivot->cantidad;

                $this->cantidadAVender = ($this->stockMaximo > 0) ? 1 : 0;
                $this->cantidadError = null;
                $this->puedeAgregar = ($this->stockMaximo > 0);
            }
        } else {
            $this->stockMaximo = 0;
            $this->cantidadAVender = 1;
            $this->cantidadError = null;
            $this->puedeAgregar = true;
        }
    }

    public function updatedCantidadAVender($value)
    {
        // No corregir automáticamente el valor ingresado. Solo validar y
        // establecer mensajes/estado para deshabilitar el botón hasta que
        // el usuario ingrese un valor válido.

        // Campo vacío: mostrar error y deshabilitar añadir
        if ($value === '' || is_null($value)) {
            $this->cantidadError = 'Ingresa un dato válido.';
            $this->puedeAgregar = false;
            return;
        }

        // Si no es numérico: mensaje y deshabilitar
        if (!is_numeric($value)) {
            $this->cantidadError = 'Ingresa un número válido.';
            $this->puedeAgregar = false;
            return;
        }

        $valorInt = (int) $value;

        // Si es menor a 1: mensaje y deshabilitar (no corregir a 1)
        if ($valorInt < 1) {
            $this->cantidadError = 'La cantidad mínima es 1.';
            $this->puedeAgregar = false;
            return;
        }

        // Si supera el stock: mensaje y deshabilitar (no corregir el input)
        if ($this->stockMaximo > 0 && $valorInt > $this->stockMaximo) {
            $this->cantidadError = 'No hay suficiente stock disponible.';
            $this->puedeAgregar = false;
            return;
        }

        // Valor válido
        $this->cantidadError = null;
        $this->puedeAgregar = true;
    }

    public function agregarAlCarrito($confirmarPrecioBajo = false)
    {
        if (!$this->puedeAgregar) {
            $this->addError('cantidadAVender', $this->cantidadError ?? 'La cantidad no es válida para agregar.');
            return;
        }

        $this->validate([
            'marcaSeleccionada' => [
                'required',
                'integer',
                Rule::exists('marca', 'id')->where('user_id', Auth::id()),
            ],
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

        $tipo = array_key_exists($this->tipoPrecioItem, self::TIPOS_PRECIO) ? $this->tipoPrecioItem : 'cliente';
        $precioManual = null;

        if ($tipo === 'manual') {
            $this->validate([
                'precioManual' => ['required', 'numeric', 'gt:0'],
            ], [
                'precioManual.required' => 'Escribe el precio que vas a dar.',
                'precioManual.numeric' => 'El precio debe ser un número.',
                'precioManual.gt' => 'El precio debe ser mayor a cero.',
            ]);

            $precioManual = round((float) $this->precioManual, 2);
            $alerta = self::alertaPrecioManual($marcaInfo->pivot, $precioManual);

            // Precio por debajo del mínimo: avisar y esperar confirmación.
            if ($alerta && !$confirmarPrecioBajo) {
                $this->alertaPrecioBajo = $alerta;
                return;
            }
        }

        $precio = self::precioSegunTipo($marcaInfo->pivot, $tipo, $precioManual);
        $tipoDescuento = self::TIPOS_PRECIO[$tipo];

        $subtotal = $precio * $this->cantidadAVender;
        // Agregamos al carrito (Array en memoria)
        $this->carrito[] = [
            'producto_id' => $this->productoId,
            'nombre'      => $this->productoSeleccionado->nombre_producto,
            'marca_id'    => $this->marcaSeleccionada,
            'marca'       => $marcaInfo->nombre_marca,
            'precio'      => $precio,
            'cantidad'    => $this->cantidadAVender,
            'subtotal'    => $subtotal,
            'tipoPrecio'  => $tipo,
            'precioManual' => $precioManual,
            'bajoMinimo'  => $tipo === 'manual' && self::alertaPrecioManual($marcaInfo->pivot, $precioManual) !== null,
            'tipoDescuento' => $tipoDescuento,
        ];

        $this->calcularTotal();
        $this->modalSeleccion = false;
        $this->reset(['marcaSeleccionada', 'cantidadAVender', 'cantidadError', 'puedeAgregar', 'precioManual', 'alertaPrecioBajo']);
    }

    public function updatedPrecioManual()
    {
        $this->alertaPrecioBajo = null;
    }

    public function updatedTipoPrecioItem()
    {
        $this->alertaPrecioBajo = null;
    }

    /**
     * Precio más bajo configurado para el producto (cliente, taller o mayoreo).
     */
    public static function precioMinimo($pivot): float
    {
        return (float) collect([$pivot->precio_cliente, $pivot->precio_taller, $pivot->precio_mayoreo])
            ->filter(fn ($p) => $p !== null && (float) $p > 0)
            ->min();
    }

    /**
     * Mensaje de alerta si el precio manual queda por debajo del costo o del
     * precio mínimo configurado. null si el precio está bien.
     */
    public static function alertaPrecioManual($pivot, $precio): ?string
    {
        if ($precio === null) {
            return null;
        }

        $precio = (float) $precio;
        $costo = $pivot->precio_costo !== null ? (float) $pivot->precio_costo : null;

        if ($costo !== null && $precio < $costo) {
            return 'El precio $' . number_format($precio, 2) . ' está POR DEBAJO DEL COSTO ($' . number_format($costo, 2) . '). Esta venta genera pérdida.';
        }

        $minimo = self::precioMinimo($pivot);

        if ($minimo > 0 && $precio < $minimo) {
            return 'El precio $' . number_format($precio, 2) . ' está por debajo del precio mínimo configurado ($' . number_format($minimo, 2) . ').';
        }

        return null;
    }

    /**
     * Precio unitario según el tipo elegido. Si el producto no tiene
     * precio de taller o mayoreo registrado, se usa el precio de cliente.
     */
    public static function precioSegunTipo($pivot, string $tipo, $precioManual = null): float
    {
        $precio = match ($tipo) {
            'manual'  => $precioManual,
            'taller'  => $pivot->precio_taller,
            'mayoreo' => $pivot->precio_mayoreo,
            default   => $pivot->precio_cliente,
        };

        return (float) ($precio ?? $pivot->precio_cliente);
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
                'clienteId' => [
                    'required',
                    'integer',
                    Rule::exists('cliente', 'id')->where('user_id', Auth::id()),
                ],
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
                'nombreInvitado' => ['nullable', 'string', 'max:255'],
                'emailFacturacion' => ['nullable', 'email', 'max:255'],
            ], [
                'nombreInvitado.string' => 'El nombre del invitado debe ser texto.',
                'nombreInvitado.max' => 'El nombre del invitado no debe superar 255 caracteres.',
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
                    'nombre_invitado' => ($this->tipoCliente == 'invitado') ? (trim((string) $this->nombreInvitado) !== '' ? trim((string) $this->nombreInvitado) : null) : null,
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
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Error al procesar venta: ' . $e->getMessage(), ['exception' => $e]);
            session()->flash('error', 'No se pudo procesar la venta. Verifica el stock disponible e inténtalo de nuevo.');
            return;
        }

        // A partir de aquí la venta YA quedó registrada en la base de datos
        // (recibo creado, stock descontado). Lo que siga (envío de correo)
        // no debe poder marcar la venta como fallida ni dejar el carrito
        // intacto, o el cajero podría repetir el cobro y duplicar la venta.
        $tipoClienteVenta = $this->tipoCliente;
        $emailFacturacionVenta = $this->emailFacturacion;

        $this->reset(['carrito', 'totalVenta', 'clienteId', 'clienteSeleccionadoNombre', 'clienteSeleccionadoDui', 'nombreInvitado', 'emailFacturacion', 'busquedaCliente', 'modalConfirmVenta', 'tipoPrecio']);
        $this->resetErrorBag();

        $this->dispatch('venta-realizada');
        $this->dispatch('abrir-ticket', url: route('recibo.pdf', $idGenerado));

        try {
            $reciboParaEmail = Recibo::with('cliente')->find($idGenerado);
            $destinatario = null;

            if ($tipoClienteVenta == 'registrado' && $reciboParaEmail && $reciboParaEmail->cliente) {
                $destinatario = $reciboParaEmail->cliente->email_cliente;
            } elseif ($tipoClienteVenta == 'invitado' && $emailFacturacionVenta) {
                $destinatario = $emailFacturacionVenta;
            }

            $correoCopia = 'sotosalvador53@gmail.com';

            // Se encola (no se envía en el mismo request) para que el cajero
            // no tenga que esperar a que responda el SMTP de Gmail antes de
            // poder seguir vendiendo. El worker de la cola (`php artisan
            // queue:work` / `composer run dev`) es quien realmente lo envía.
            if ($destinatario && strcasecmp($destinatario, $correoCopia) !== 0) {
                Mail::to($destinatario)
                    ->cc($correoCopia)
                    ->queue(new EnviarReciboMailable($reciboParaEmail));
            } else {
                Mail::to($correoCopia)->queue(new EnviarReciboMailable($reciboParaEmail));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Venta #{$idGenerado} registrada, pero falló el envío del recibo por correo: " . $e->getMessage(), ['exception' => $e]);
            session()->flash('advertencia', "La venta #{$idGenerado} se registró correctamente, pero no se pudo enviar el recibo por correo. Puedes descargarlo desde el ticket.");
        }
    }

    public function updatedTipoCliente()
    {
        $this->reset(['clienteId', 'clienteSeleccionadoNombre', 'clienteSeleccionadoDui', 'nombreInvitado', 'emailFacturacion', 'busquedaCliente']);
        $this->listaClientesCacheada = [];
        $this->resetErrorBag();
    }

    /**
     * Al cambiar el precio predeterminado se aplica a todo el ticket.
     */
    public function updatedTipoPrecio($value)
    {
        if (!array_key_exists($value, self::TIPOS_PRECIO) || $value === 'manual') {
            $this->tipoPrecio = 'cliente';
        }

        // Los precios escritos a mano no se sobrescriben.
        foreach ($this->carrito as $index => $item) {
            if (($item['tipoPrecio'] ?? null) !== 'manual') {
                $this->carrito[$index]['tipoPrecio'] = $this->tipoPrecio;
            }
        }

        $this->recalcularPreciosCarrito();
    }

    /**
     * Cambio de tipo de precio en una sola línea del ticket
     * (wire:model="carrito.N.tipoPrecio").
     */
    public function updatedCarrito($value, $key)
    {
        if (!str_ends_with((string) $key, '.tipoPrecio')) {
            return;
        }

        $index = (int) explode('.', $key)[0];

        if (isset($this->carrito[$index])) {
            $valido = array_key_exists($value, self::TIPOS_PRECIO)
                && ($value !== 'manual' || ($this->carrito[$index]['precioManual'] ?? null) !== null);

            if (!$valido) {
                $this->carrito[$index]['tipoPrecio'] = 'cliente';
            }
        }

        $this->recalcularPreciosCarrito();
    }

    private function recalcularPreciosCarrito()
    {
        if (empty($this->carrito)) {
            return;
        }

        $productosPorId = Producto::with('marcas')
            ->whereIn('id', collect($this->carrito)->pluck('producto_id')->unique()->values())
            ->get()
            ->keyBy('id');

        foreach ($this->carrito as $index => $item) {
            $marcaInfo = $productosPorId->get($item['producto_id'])?->marcas->where('id', $item['marca_id'])->first();

            if (!$marcaInfo) {
                continue;
            }

            $tipo = $item['tipoPrecio'] ?? 'cliente';
            $precio = self::precioSegunTipo($marcaInfo->pivot, $tipo, $item['precioManual'] ?? null);

            $this->carrito[$index]['precio'] = $precio;
            $this->carrito[$index]['bajoMinimo'] = $tipo === 'manual' && self::alertaPrecioManual($marcaInfo->pivot, $precio) !== null;
            $this->carrito[$index]['subtotal'] = $precio * $item['cantidad'];
            $this->carrito[$index]['tipoDescuento'] = self::TIPOS_PRECIO[$tipo];
        }

        $this->calcularTotal();
    }

    public function cerrarModal()
    {
        $this->reset([
            'modalSeleccion',
            'productoId',
            'productoSeleccionado',
            'marcaSeleccionada',
            'cantidadAVender',
            'stockMaximo',
            'cantidadError',
            'puedeAgregar',
            'tipoPrecioItem',
            'precioManual',
            'alertaPrecioBajo',
        ]);
    }
}
