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
                        <x-input type="number" class="w-full" wire:model.live="cantidadAVender" min="1" max="{{$stockMaximo}}" />
                        <div>
                            @if($marcaSeleccionada)
                            <span class="text-xs font-bold {{ $cantidadAVender > $stockMaximo ? 'text-red-500' : 'text-green-400' }}">
                                Máx: {{ $stockMaximo }}
                            </span>
                            @endif
                        </div>
                        <div>
                            @if ($cantidadAVender > $stockMaximo && $marcaSeleccionada)
                            <span class="text-xs font-bold text-red-500">
                                No puedes vender más de {{ $stockMaximo }} unidades.
                            </span>
                            @endif
                        </div>
                        <div>
                            @if ($cantidadAVender=="" || $cantidadAVender < 1 && $marcaSeleccionada)
                                <span class="text-xs font-bold text-red-500">
                                Ingresa un dato valido
                                </span>
                                @endif
                        </div>
                    </div>
                    <x-input-error for="cantidadAVender" class="mt-1" />

                    {{-- ← NUEVO: Notificación de precio mayoreo --}}
                    @php
                    $marcaActual = $productoSeleccionado?->marcas->where('id', $marcaSeleccionada)->first();
                    @endphp

                    @if ($marcaActual)
                    @if ($cantidadAVender >= $marcaActual->pivot->cantidad_mayoreo)
                    {{-- Ya aplica mayoreo --}}
                    <div class="mt-2 flex items-center gap-2 rounded-lg border border-green-200 bg-green-50 px-3 py-2">
                        <svg class="h-4 w-4 shrink-0 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-xs font-semibold text-green-700">
                            ¡Precio de mayoreo aplicado!
                            <span class="font-black">${{ number_format($marcaActual->pivot->precio_mayoreo, 2) }}</span>
                            por unidad.
                        </p>
                    </div>
                    @else
                    {{-- Falta cantidad para mayoreo --}}
                    <div class="mt-2 flex items-center gap-2 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2">
                        <svg class="h-4 w-4 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z" />
                        </svg>
                        <p class="text-xs text-amber-700">
                            Compra
                            <span class="font-black">{{ $marcaActual->pivot->cantidad_mayoreo - $cantidadAVender }}</span>
                            unidad(es) más para obtener precio de mayoreo
                            (<span class="font-black">${{ number_format($marcaActual->pivot->precio_mayoreo, 2) }}</span> c/u
                            al comprar {{ $marcaActual->pivot->cantidad_mayoreo }} o más).
                        </p>
                    </div>
                    @endif
                    @endif

                </div>
                @endif
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="cerrarModal">Cancelar</x-secondary-button>
            @if ($marcaSeleccionada && $cantidadAVender > 0 && $cantidadAVender <= $stockMaximo)
                <x-button wire:click="agregarAlCarrito" class="ml-3">Añadir al Ticket</x-button>
                @endif
        </x-slot>
    </x-dialog-modal>

    <x-confirmation-modal wire:model.live="modalConfirmVenta">
        <x-slot name="title">Confirmar Pago</x-slot>
        <x-slot name="content">
            @error('carrito')
            <div class="mb-3 text-sm text-red-600">{{ $message }}</div>
            @enderror
            ¿Desea finalizar la venta por un total de <span class="font-bold text-lg">${{ number_format($totalVenta, 2) }}</span>?
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

                        <div class="grid grid-cols-2 sm:flex bg-gray-100 p-1 rounded-lg w-full sm:w-auto gap-1 sm:gap-0">
                            <button wire:click="$set('tipoCliente', 'registrado')"
                                class="px-3 py-1 text-xs font-bold rounded-md {{ $tipoCliente == 'registrado' ? 'bg-white shadow text-indigo-600' : 'text-gray-500' }}">
                                Registrado
                            </button>
                            <button wire:click="$set('tipoCliente', 'invitado')"
                                class="px-3 py-1 text-xs font-bold rounded-md {{ $tipoCliente == 'invitado' ? 'bg-white shadow text-indigo-600' : 'text-gray-500' }}">
                                Invitado
                            </button>
                        </div>
                    </div>

                    @if($tipoCliente == 'registrado')
                    <div class="space-y-3">
                        <div>
                            <x-label value="Buscar Cliente (Nombre o DUI)" />
                            <x-input type="text" wire:model.live.debounce.400ms="busquedaCliente" class="w-full text-sm" placeholder="Ej: Juan Pérez o 00000000-0" />
                            <x-input-error for="clienteId" class="mt-1" />
                        </div>

                        @if($busquedaCliente != '')
                        <div class="border rounded-md divide-y max-h-40 overflow-y-auto">
                            @forelse($listaClientes as $cliente)
                            <div wire:click="$set('clienteId', {{ $cliente->id }}); $set('busquedaCliente', '{{ $cliente->nombres_cliente }} {{ $cliente->apellidos_cliente }}')"
                                class="p-2 text-sm hover:bg-indigo-50 cursor-pointer {{ $clienteId == $cliente->id ? 'bg-indigo-100' : '' }}">
                                <p class="font-bold">{{ $cliente->nombres_cliente }} {{ $cliente->apellidos_cliente }}</p>
                                <p class="text-xs text-gray-500">DUI: {{ $cliente->dui_cliente }}</p>
                            </div>
                            @empty
                            <p class="p-2 text-xs text-gray-500">No se encontraron clientes.</p>
                            @endforelse
                        </div>
                        @endif
                    </div>
                    @else
                    <div class="animate-fadeIn">
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
                    <h3 class="font-black text-gray-700 uppercase tracking-wider mb-4 border-b pb-2">Ticket de Venta</h3>
                    <div class="space-y-4 mb-6">
                        @error('carrito')
                        <div class="text-sm text-red-600">{{ $message }}</div>
                        @enderror
                        @forelse($carrito as $indice => $item)
                        <div class="flex justify-between items-start text-sm">
                            <div class="flex-1">
                                <p class="font-bold text-gray-800">{{ $item['nombre'] }}</p>
                                <p class="text-xs text-gray-400">{{ $item['marca'] }} ({{ $item['cantidad'] }} x ${{ $item['precio'] }})</p>
                            </div>
                            <div class="text-right ml-4">
                                <p class="font-bold text-indigo-600">${{ number_format($item['subtotal'], 2) }}</p>
                                <button wire:click="quitarDelCarrito({{ $indice }})" class="text-[10px] text-red-500 hover:underline uppercase">Remover</button>
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