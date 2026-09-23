<div class="py-5 px-4 sm:px-6 lg:px-8">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nueva Venta') }}
        </h2>
    </x-slot>

    {{-- Notificaciones --}}
    <x-action-message class="mr-3" on="venta-realizada">
        {{ __('Venta procesada con éxito!') }}
    </x-action-message>

    @if (session()->has('error'))
    <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        {{ session('error') }}
    </div>
    @endif

    @if (session()->has('advertencia'))
    <div class="mb-4 rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
        ⚠️ {{ session('advertencia') }}
    </div>
    @endif

    {{-- Modales --}}
    <x-dialog-modal wire:model.live="modalSeleccion">
        <x-slot name="title">Configurar Producto</x-slot>
        <x-slot name="content">
            <div class="grid grid-cols-1 gap-4">
                @if($productoSeleccionado)
                <div class="p-3 bg-gray-50 rounded text-sm mb-2">
                    <strong>Producto:</strong> {{ $productoSeleccionado->nombre_producto }}
                </div>
                @endif

                <div>
                    <x-label value="Seleccionar Marca" />
                    <select wire:model.live="marcaSeleccionada" class="w-full rounded-md border-gray-300">
                        <option value="">-- Seleccione Marca --</option>
                        @if($productoSeleccionado)
                        @foreach($productoSeleccionado->marcas as $m)
                        @if ($m->pivot->cantidad == 0)
                        <option disabled value="{{ $m->id }}" class="text-red-500">
                            {{ $m->nombre_marca }} (Disp: {{ $m->pivot->cantidad }}) - ${{ number_format($m->pivot->precio_cliente, 2)}} - sin stock
                        </option>
                        @else
                        <option value="{{ $m->id }}">
                            {{ $m->nombre_marca }} (Disp: {{ $m->pivot->cantidad }}) - ${{ number_format($m->pivot->precio_cliente, 2) }}
                        </option>
                        @endif
                        @endforeach
                        @endif
                    </select>
                    <x-input-error for="marcaSeleccionada" class="mt-1" />
                </div>

                @if ($marcaSeleccionada)
                    <div>
                        <x-label value="Cantidad" />
                        <div class="relative">
                            <x-input type="number" class="w-full" wire:model.live="cantidadAVender" />
                            <div>
                                @if($marcaSeleccionada)
                                <span class="text-xs font-bold {{ (is_numeric($cantidadAVender) && $cantidadAVender > $stockMaximo) ? 'text-red-500' : 'text-green-400' }}">
                                    Máx: {{ $stockMaximo }}
                                </span>
                                @endif
                            </div>
                            <div>
                                @if ($cantidadError)
                                <span class="text-xs font-bold text-red-500">
                                    {{ $cantidadError }}
                                </span>
                                @else
                                    @if ($marcaSeleccionada && is_numeric($cantidadAVender) && $cantidadAVender > $stockMaximo)
                                    <span class="text-xs font-bold text-red-500">
                                        No puedes vender más de {{ $stockMaximo }} unidades.
                                    </span>
                                    @endif
                                @endif
                            </div>
                        </div>
                    <x-input-error for="cantidadAVender" class="mt-1" />

                    @php
                    $marcaActual = $productoSeleccionado?->marcas->where('id', $marcaSeleccionada)->first();
                    @endphp

                    @if ($marcaActual)
                    @php
                    $pv = $marcaActual->pivot;
                    $precioManualNum = is_numeric($precioManual) ? (float) $precioManual : null;
                    $precioElegido = $tipoPrecioItem === 'manual'
                        ? ($precioManualNum ?? 0)
                        : \App\Livewire\Ventas::precioSegunTipo($pv, $tipoPrecioItem);
                    $cantidadNum = is_numeric($cantidadAVender) ? (int) $cantidadAVender : 0;
                    @endphp
                    <div class="mt-4">
                        <x-label for="tipoPrecioItem" value="Tipo de precio" />
                        <select id="tipoPrecioItem" wire:model.live="tipoPrecioItem" class="w-full rounded-md border-gray-300">
                            <option value="cliente">Precio Cliente — ${{ number_format($pv->precio_cliente, 2) }}</option>
                            <option value="taller">Precio Taller — {{ $pv->precio_taller !== null ? '$' . number_format($pv->precio_taller, 2) : 'no registrado (usa precio cliente)' }}</option>
                            <option value="mayoreo">Precio Mayoreo — ${{ number_format($pv->precio_mayoreo, 2) }} (desde {{ $pv->cantidad_mayoreo }} u.)</option>
                            <option value="manual">Precio Manual — escribir precio</option>
                        </select>

                        @if ($tipoPrecioItem === 'manual')
                        <div class="mt-3">
                            <x-label for="precioManual" value="Precio por unidad" />
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">$</span>
                                <x-input id="precioManual" type="number" step="0.01" min="0" class="w-full pl-7" wire:model.live.debounce.400ms="precioManual" placeholder="0.00" />
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                Mínimo configurado: ${{ number_format(\App\Livewire\Ventas::precioMinimo($pv), 2) }}
                                @if ($pv->precio_costo !== null) · Costo: ${{ number_format($pv->precio_costo, 2) }} @endif
                            </p>
                            <x-input-error for="precioManual" class="mt-1" />

                            @if (!$alertaPrecioBajo && $precioManualNum !== null && \App\Livewire\Ventas::alertaPrecioManual($pv, $precioManualNum))
                            <p class="mt-2 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-700">
                                {{ \App\Livewire\Ventas::alertaPrecioManual($pv, $precioManualNum) }}
                            </p>
                            @endif
                        </div>
                        @endif

                        @if ($alertaPrecioBajo)
                        <div class="mt-3 rounded-lg border-2 border-red-300 bg-red-50 p-3" role="alert">
                            <p class="text-sm font-bold text-red-800">Precio muy bajo</p>
                            <p class="mt-1 text-sm text-red-700">{{ $alertaPrecioBajo }}</p>
                            <p class="mt-1 text-sm text-red-700">¿Deseas agregarlo al ticket de todas formas?</p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <button type="button" wire:click="agregarAlCarrito(true)" class="rounded-md bg-red-600 px-3 py-2 text-xs font-bold text-white hover:bg-red-700">
                                    Sí, vender a este precio
                                </button>
                                <button type="button" wire:click="$set('alertaPrecioBajo', null)" class="rounded-md border border-gray-300 bg-white px-3 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50">
                                    No, cambiar precio
                                </button>
                            </div>
                        </div>
                        @endif

                        <div class="mt-2 flex items-center justify-between rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm">
                            <span class="text-gray-600">{{ $cantidadNum }} x ${{ number_format($precioElegido, 2) }}</span>
                            <span class="font-black text-gray-900">${{ number_format($precioElegido * $cantidadNum, 2) }}</span>
                        </div>

                        @if ($tipoPrecioItem === 'mayoreo' && $cantidadNum < $pv->cantidad_mayoreo)
                        <p class="mt-2 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-700">
                            El mayoreo normalmente aplica desde {{ $pv->cantidad_mayoreo }} unidades; estás vendiendo {{ $cantidadNum }}.
                        </p>
                        @endif

                        @if ($pv->precio_taller !== null && (float) $pv->precio_taller > (float) $pv->precio_cliente)
                        <p class="mt-2 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-700">
                            Revisa este producto: su precio de taller (${{ number_format($pv->precio_taller, 2) }}) es mayor que el de cliente (${{ number_format($pv->precio_cliente, 2) }}).
                        </p>
                        @endif
                    </div>
                    @endif

                </div>
                @endif
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="cerrarModal">Cancelar</x-secondary-button>
            @if ($marcaSeleccionada && !$alertaPrecioBajo)
                <x-button wire:click="agregarAlCarrito" class="ml-3" :disabled="!$puedeAgregar" wire:loading.attr="disabled" wire:target="agregarAlCarrito">
                    <span wire:loading.remove wire:target="agregarAlCarrito">Añadir al Ticket</span>
                    <span wire:loading wire:target="agregarAlCarrito">Añadiendo...</span>
                </x-button>
            @endif
        </x-slot>
    </x-dialog-modal>

    <x-confirmation-modal wire:model.live="modalConfirmVenta">
        <x-slot name="title">Confirmar Venta</x-slot>
        <x-slot name="content">
            @error('carrito')
            <div class="mb-3 text-sm text-red-600">{{ $message }}</div>
            @enderror
            
            <div class="space-y-4">
                {{-- Resumen de Productos --}}
                <div class="border-t pt-3">
                    <p class="text-xs font-bold text-gray-600 uppercase mb-2">Detalle de Productos:</p>
                    <div class="space-y-2 max-h-48 overflow-y-auto">
                        @foreach($carrito as $item)
                        <div class="bg-gray-50 p-2 rounded border border-gray-200 text-xs">
                            <div class="flex justify-between items-start mb-1">
                                <span class="font-bold text-gray-900">{{ $item['nombre'] }}</span>
                                <span class="font-bold text-indigo-600">${{ number_format($item['subtotal'], 2) }}</span>
                            </div>
                            <div class="text-gray-600 text-[11px]">
                                <span class="font-semibold">{{ $item['marca'] }}</span> | {{ $item['cantidad'] }} x ${{ number_format($item['precio'], 2) }}
                            </div>
                            <div class="text-[10px]">
                                <span class="inline-block px-2 py-0.5 bg-blue-100 text-blue-700 rounded font-semibold">{{ $item['tipoDescuento'] }}</span>
                                @if (!empty($item['bajoMinimo']))
                                <span class="inline-block px-2 py-0.5 bg-red-100 text-red-700 rounded font-semibold">Debajo del mínimo</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Total --}}
                <div class="border-t-2 border-dashed pt-3">
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-gray-700 text-sm">TOTAL A COBRAR:</span>
                        <span class="text-2xl font-black text-green-600">${{ number_format($totalVenta, 2) }}</span>
                    </div>
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('modalConfirmVenta', false)" wire:loading.attr="disabled" wire:target="guardarVenta">Revisar</x-secondary-button>
            <x-button wire:click="guardarVenta" wire:loading.attr="disabled" wire:target="guardarVenta" class="ml-3 bg-green-600">
                <span wire:loading.remove wire:target="guardarVenta">Cobrar Ahora</span>
                <span wire:loading wire:target="guardarVenta">Procesando compra...</span>
            </x-button>
        </x-slot>
    </x-confirmation-modal>

    {{-- Contenido Principal --}}
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white p-6 rounded-lg shadow-sm border">
                    <x-input type="text" class="w-full mb-4" placeholder="Buscar producto por nombre..." wire:model.live.debounce.400ms="buscador" />

                    <x-table>
                        <x-slot name="thead">
                            <x-th>ID</x-th>
                            <x-th>Nombre</x-th>
                            <x-th>Stock</x-th>
                            <x-th class="text-right">Acción</x-th>
                        </x-slot>

                        @foreach ($productos as $producto)
                        <x-tr>
                            <x-td>{{ $producto->id }}</x-td>
                            <x-td class="font-bold">{{ $producto->nombre_producto }}</x-td>
                            <x-td>
                                <span class="text-xs text-gray-500">
                                    {{ $producto->marcas->sum('pivot.cantidad') }} unidades
                                </span>
                            </x-td>
                            <x-td class="text-right">
                                <x-button wire:click="seleccionarProducto({{ $producto->id }})">Vender</x-button>
                            </x-td>
                        </x-tr>
                        @endforeach
                    </x-table>
                    <div class="mt-4">{{ $productos->links() }}</div>
                </div>
            </div>

            <div class="lg:col-span-1">

                {{--Clientes--}}
                <div class="bg-white p-6 rounded-lg shadow-md border-b-4 border-indigo-500 mb-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-4">
                        <h3 class="font-bold text-gray-700 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Facturación
                        </h3>

                        <select wire:model.live="tipoCliente" aria-label="Tipo de cliente" class="w-full sm:w-auto rounded-md border-gray-300 text-sm">
                            <option value="registrado">Cliente registrado</option>
                            <option value="invitado">Cliente invitado</option>
                        </select>
                    </div>

                    @if($tipoCliente == 'registrado')
                    <div class="space-y-3">
                        @if($clienteId && $clienteSeleccionadoNombre)
                        {{-- Cliente elegido: queda fijo hasta que se quite --}}
                        <div class="flex items-start justify-between gap-3 rounded-lg border-2 border-indigo-300 bg-indigo-50 p-3">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wide text-indigo-600">Cliente seleccionado</p>
                                <p class="font-bold text-gray-900">{{ $clienteSeleccionadoNombre }}</p>
                                @if($clienteSeleccionadoDui)
                                <p class="text-xs text-gray-500">DUI: {{ $clienteSeleccionadoDui }}</p>
                                @endif
                            </div>
                            <button type="button" wire:click="quitarCliente" class="shrink-0 rounded-md border border-gray-300 bg-white px-2 py-1 text-xs font-bold text-gray-600 hover:bg-gray-50">
                                Cambiar
                            </button>
                        </div>
                        @else
                        <div>
                            <x-label value="Buscar Cliente (Nombre o DUI)" />
                            <x-input type="text" wire:model.live.debounce.400ms="busquedaCliente" class="w-full text-sm" placeholder="Ej: Juan Pérez o 00000000-0" />
                            <x-input-error for="clienteId" class="mt-1" />
                        </div>

                        @if($busquedaCliente != '')
                        <div class="border rounded-md divide-y max-h-40 overflow-y-auto">
                            @forelse($listaClientes as $cliente)
                            <button type="button" wire:key="cliente-{{ $cliente->id }}" wire:click="seleccionarCliente({{ $cliente->id }})"
                                class="block w-full p-2 text-left text-sm hover:bg-indigo-50">
                                <p class="font-bold">{{ $cliente->nombres_cliente }} {{ $cliente->apellidos_cliente }}</p>
                                <p class="text-xs text-gray-500">DUI: {{ $cliente->dui_cliente }}</p>
                            </button>
                            @empty
                            <p class="p-2 text-xs text-gray-500">No se encontraron clientes.</p>
                            @endforelse
                        </div>
                        @endif
                        @endif
                    </div>
                    @else
                    <div class="animate-fadeIn">
                        <x-label value="Nombre del Cliente Invitado (Opcional)" />
                        <x-input type="text" wire:model="nombreInvitado" class="w-full text-sm" placeholder="Ej: Cliente de Mostrador" />
                        <x-input-error for="nombreInvitado" class="mt-1" />

                        <div class="mt-3"></div>
                        <x-label value="Correo para Factura Electrónica (Opcional)" />
                        <x-input type="email" wire:model="emailFacturacion" class="w-full text-sm" placeholder="correo@cliente.com" />
                        <x-input-error for="emailFacturacion" class="mt-1" />
                        <div class="mt-2 p-2 bg-blue-50 rounded border border-blue-100">
                            <p class="text-[10px] text-blue-700 leading-tight uppercase font-bold">
                                Modo: Consumidor Final / Cliente no registrado
                            </p>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-indigo-600 lg:sticky lg:top-6">
                    <div class="mb-4">
                        <x-label for="tipoPrecio" value="Precio para esta venta" />
                        <select id="tipoPrecio" wire:model.live="tipoPrecio" class="w-full rounded-md border-gray-300">
                            <option value="cliente">Precio Cliente</option>
                            <option value="taller">Precio Taller</option>
                            <option value="mayoreo">Precio Mayoreo</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Se aplica a todo el ticket. Puedes cambiarlo por producto abajo.</p>
                    </div>

                    <h3 class="font-black text-gray-700 uppercase tracking-wider mb-4 border-b pb-2">Ticket de Venta</h3>
                    <div class="space-y-4 mb-6">
                        @error('carrito')
                        <div class="text-sm text-red-600">{{ $message }}</div>
                        @enderror
                        @forelse($carrito as $indice => $item)
                        <div class="flex justify-between items-start text-sm border border-gray-200 p-3 rounded-lg bg-gray-50 hover:bg-gray-100 transition">
                            <div class="flex-1">
                                <p class="font-bold text-gray-800">{{ $item['nombre'] }}</p>
                                <p class="text-xs text-gray-500">{{ $item['marca'] }} | {{ $item['cantidad'] }} x ${{ number_format($item['precio'], 2) }}</p>
                                <select wire:model.live="carrito.{{ $indice }}.tipoPrecio" aria-label="Tipo de precio" class="mt-1 rounded-md border-gray-300 py-1 pl-2 pr-8 text-xs">
                                    <option value="cliente">Precio Cliente</option>
                                    <option value="taller">Precio Taller</option>
                                    <option value="mayoreo">Precio Mayoreo</option>
                                    @if(($item['precioManual'] ?? null) !== null)
                                    <option value="manual">Precio Manual (${{ number_format($item['precioManual'], 2) }})</option>
                                    @endif
                                </select>
                                @if (!empty($item['bajoMinimo']))
                                <span class="ml-1 inline-block rounded bg-red-100 px-2 py-0.5 text-[10px] font-semibold text-red-700">Debajo del mínimo</span>
                                @endif
                            </div>
                            <div class="text-right ml-4">
                                <p class="font-bold text-indigo-600">${{ number_format($item['subtotal'], 2) }}</p>
                                <button wire:click="quitarDelCarrito({{ $indice }})" class="text-[10px] text-red-500 hover:underline uppercase mt-1">Remover</button>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-10">
                            <p class="text-gray-400 italic">No hay productos seleccionados</p>
                        </div>
                        @endforelse
                    </div>

                    <div class="border-t-2 border-dashed pt-4 mb-6">
                        <div class="flex justify-between items-center text-2xl font-black text-gray-900">
                            <span>TOTAL:</span>
                            <span>${{ number_format($totalVenta, 2) }}</span>
                        </div>
                    </div>

                    <x-button class="w-full justify-center py-4 text-lg bg-green-600 hover:bg-green-700"
                        wire:click="abrirConfirmacionVenta"
                        :disabled="empty($carrito)">
                        PROCESAR COBRO
                    </x-button>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            window.addEventListener('abrir-ticket', (event) => {
                if (event.detail.url) {
                    window.open(event.detail.url, '_blank');
                }
            });
        });
    </script>

    <div wire:loading.flex wire:target="guardarVenta" class="fixed inset-0 z-[100] items-center justify-center bg-black/40 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl px-8 py-6 text-center max-w-sm w-[90%]">
            <div class="mx-auto mb-4 h-10 w-10 rounded-full border-4 border-gray-200 border-t-indigo-600 animate-spin"></div>
            <p class="text-lg font-bold text-gray-800">Procesando compra...</p>
            <p class="text-sm text-gray-500 mt-1">Generando ticket y enviando correo con PDF</p>
        </div>
    </div>
</div>