<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

    <x-action-message class="mr-3" on="recepcion-confirmada">
        Recepción confirmada. El stock ha sido actualizado.
    </x-action-message>

    {{-- Modal: agregar detalle (solo modo crear) --}}
    @if (!$modoEdicion)
    <x-dialog-modal wire:model.live="modalAgregarDetalle">
        <x-slot name="title">Agregar Producto a la Factura</x-slot>

        <x-slot name="content">
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <x-label value="Producto *" />
                    <select wire:model.live="agregarProductoId"
                            class="mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                        <option value="">— Selecciona un producto —</option>
                        @foreach ($productos as $prod)
                            <option value="{{ $prod->id }}">{{ $prod->nombre_producto }}</option>
                        @endforeach
                    </select>
                    <x-input-error for="agregarProductoId" class="mt-1" />
                </div>

                <div>
                    <x-label value="Marca *" />
                    <select wire:model="agregarMarcaId"
                            class="mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                            @if(empty($marcasDisponibles)) disabled @endif>
                        <option value="">— Selecciona una marca —</option>
                        @foreach ($marcasDisponibles as $m)
                            <option value="{{ $m['id'] }}">{{ $m['nombre_marca'] }}</option>
                        @endforeach
                    </select>
                    <x-input-error for="agregarMarcaId" class="mt-1" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-label value="Cantidad esperada *" />
                        <x-input wire:model="agregarCantidadEsperada" type="number" min="1"
                                 class="w-full mt-1" placeholder="1" />
                        <x-input-error for="agregarCantidadEsperada" class="mt-1" />
                    </div>
                    <div>
                        <x-label value="Precio unitario ($) *" />
                        <x-input wire:model="agregarPrecioUnitario" type="number" min="0.01" step="0.01"
                                 class="w-full mt-1" placeholder="0.00" />
                        <x-input-error for="agregarPrecioUnitario" class="mt-1" />
                    </div>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('modalAgregarDetalle', false)">Cancelar</x-secondary-button>
            <x-button wire:click="agregarDetalle" class="ml-3">Agregar</x-button>
        </x-slot>
    </x-dialog-modal>
    @endif

    {{-- Modal: confirmar recepción --}}
    <x-dialog-modal wire:model.live="modalConfirmar">
        <x-slot name="title">Confirmar Recepción de Mercancía</x-slot>

        <x-slot name="content">
            <p class="text-sm text-gray-600 mb-4">
                Ingresa la cantidad recibida para cada producto. El stock se actualizará automáticamente.
            </p>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <th class="px-3 py-2">Producto</th>
                            <th class="px-3 py-2">Marca</th>
                            <th class="px-3 py-2 text-center">Esperado</th>
                            <th class="px-3 py-2 text-center">Ya recibido</th>
                            <th class="px-3 py-2 text-center">Recibir ahora</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @if ($facturaCompra)
                            @foreach ($facturaCompra->detalles as $detalle)
                                <tr>
                                    <td class="px-3 py-2 font-medium text-gray-800">
                                        {{ $detalle->producto->nombre_producto ?? '—' }}
                                    </td>
                                    <td class="px-3 py-2 text-gray-600">
                                        {{ $detalle->marca->nombre_marca ?? '—' }}
                                    </td>
                                    <td class="px-3 py-2 text-center text-gray-700">
                                        {{ $detalle->cantidad_esperada }}
                                    </td>
                                    <td class="px-3 py-2 text-center text-gray-500">
                                        {{ $detalle->cantidad_recibida }}
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <input
                                            type="number"
                                            wire:model="cantidadesRecibidas.{{ $detalle->id }}"
                                            min="0"
                                            max="{{ $detalle->cantidad_esperada }}"
                                            class="w-20 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm text-center" />
                                        <x-input-error for="cantidadesRecibidas.{{ $detalle->id }}" class="mt-1" />
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('modalConfirmar', false)">Cancelar</x-secondary-button>
            <x-button wire:click="confirmarRecepcion" class="ml-3">Confirmar Recepción</x-button>
        </x-slot>
    </x-dialog-modal>

    {{-- Header --}}
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a wire:navigate href="{{ route('lista-recepciones') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $modoEdicion ? 'Detalle de Factura' : 'Nueva Factura de Compra' }}
            </h2>
        </div>
    </x-slot>

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md text-sm">{{ session('error') }}</div>
    @endif

    {{-- Cabecera de la factura --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-base font-semibold text-gray-700 mb-4">Datos de la Factura</h3>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <x-label value="N° de Factura *" />
                <x-input wire:model="numeroFactura"
                         class="w-full mt-1"
                         placeholder="Ej. FAC-00123"
                         :disabled="$modoEdicion" />
                <x-input-error for="numeroFactura" class="mt-1" />
            </div>

            <div>
                <x-label value="Fecha *" />
                <x-input wire:model="fecha"
                         type="date"
                         class="w-full mt-1"
                         :disabled="$modoEdicion" />
                <x-input-error for="fecha" class="mt-1" />
            </div>

            <div>
                <x-label value="Proveedor *" />
                <select wire:model="proveedorId"
                        class="mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm
                               {{ $modoEdicion ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}"
                        @if($modoEdicion) disabled @endif>
                    <option value="">— Selecciona un proveedor —</option>
                    @foreach ($proveedores as $prov)
                        <option value="{{ $prov->id }}">{{ $prov->nombre_proveedor }}</option>
                    @endforeach
                </select>
                <x-input-error for="proveedorId" class="mt-1" />
            </div>
        </div>

        @if ($modoEdicion && $facturaCompra)
            <div class="mt-4 flex items-center gap-3">
                <span class="text-sm text-gray-500">Estado:</span>
                @php
                    $badgeClass = match($facturaCompra->estado) {
                        'recibida' => 'bg-emerald-100 text-emerald-700',
                        'parcial'  => 'bg-blue-100 text-blue-700',
                        default    => 'bg-amber-100 text-amber-700',
                    };
                    $badgeLabel = match($facturaCompra->estado) {
                        'recibida' => 'Recibida',
                        'parcial'  => 'Parcial',
                        default    => 'Pendiente',
                    };
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $badgeClass }}">
                    {{ $badgeLabel }}
                </span>
            </div>
        @endif
    </div>

    {{-- Detalles --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-700">Productos</h3>

            @if (!$modoEdicion)
                <button wire:click="abrirModalDetalle"
                        class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-50 text-indigo-700 text-sm font-medium rounded-md hover:bg-indigo-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Agregar Producto
                </button>
            @endif
        </div>

        <x-input-error for="detalles" class="mb-3" />

        {{-- Tabla detalles modo CREAR (array en memoria) --}}
        @if (!$modoEdicion)
            @if (count($detalles) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <th class="px-3 py-2">Producto</th>
                                <th class="px-3 py-2">Marca</th>
                                <th class="px-3 py-2 text-center">Cant. esperada</th>
                                <th class="px-3 py-2 text-right">Precio unitario</th>
                                <th class="px-3 py-2 text-right">Subtotal</th>
                                <th class="px-3 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($detalles as $i => $item)
                                <tr>
                                    <td class="px-3 py-2 font-medium text-gray-800">{{ $item['nombre_producto'] }}</td>
                                    <td class="px-3 py-2 text-gray-600">{{ $item['nombre_marca'] }}</td>
                                    <td class="px-3 py-2 text-center text-gray-700">{{ $item['cantidad_esperada'] }}</td>
                                    <td class="px-3 py-2 text-right text-gray-700">
                                        ${{ number_format($item['precio_unitario'], 2) }}
                                    </td>
                                    <td class="px-3 py-2 text-right font-medium text-gray-800">
                                        ${{ number_format($item['precio_unitario'] * $item['cantidad_esperada'], 2) }}
                                    </td>
                                    <td class="px-3 py-2 text-right">
                                        <button wire:click="quitarDetalle({{ $i }})"
                                                class="text-red-500 hover:text-red-700 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50">
                                <td colspan="4" class="px-3 py-2 text-right font-semibold text-gray-700">Total estimado:</td>
                                <td class="px-3 py-2 text-right font-bold text-gray-900">
                                    ${{ number_format(collect($detalles)->sum(fn($d) => $d['precio_unitario'] * $d['cantidad_esperada']), 2) }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <p class="text-center text-gray-400 py-8 text-sm">
                    No hay productos agregados. Haz clic en "Agregar Producto" para comenzar.
                </p>
            @endif
        @endif

        {{-- Tabla detalles modo VER (desde BD) --}}
        @if ($modoEdicion && $facturaCompra)
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <th class="px-3 py-2">Producto</th>
                            <th class="px-3 py-2">Marca</th>
                            <th class="px-3 py-2 text-center">Esperado</th>
                            <th class="px-3 py-2 text-center">Recibido</th>
                            <th class="px-3 py-2 text-right">Precio unitario</th>
                            <th class="px-3 py-2 text-right">Subtotal</th>
                            <th class="px-3 py-2 text-center">Estado línea</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($facturaCompra->detalles as $detalle)
                            @php
                                $completo = $detalle->cantidad_recibida >= $detalle->cantidad_esperada;
                                $parcial  = $detalle->cantidad_recibida > 0 && !$completo;
                            @endphp
                            <tr>
                                <td class="px-3 py-2 font-medium text-gray-800">
                                    {{ $detalle->producto->nombre_producto ?? '—' }}
                                </td>
                                <td class="px-3 py-2 text-gray-600">
                                    {{ $detalle->marca->nombre_marca ?? '—' }}
                                </td>
                                <td class="px-3 py-2 text-center text-gray-700">{{ $detalle->cantidad_esperada }}</td>
                                <td class="px-3 py-2 text-center font-medium
                                    {{ $completo ? 'text-emerald-600' : ($parcial ? 'text-blue-600' : 'text-gray-400') }}">
                                    {{ $detalle->cantidad_recibida }}
                                </td>
                                <td class="px-3 py-2 text-right text-gray-700">
                                    ${{ number_format($detalle->precio_unitario, 2) }}
                                </td>
                                <td class="px-3 py-2 text-right font-medium text-gray-800">
                                    ${{ number_format($detalle->precio_unitario * $detalle->cantidad_esperada, 2) }}
                                </td>
                                <td class="px-3 py-2 text-center">
                                    @if ($completo)
                                        <span class="text-xs font-medium text-emerald-600">Completo</span>
                                    @elseif ($parcial)
                                        <span class="text-xs font-medium text-blue-600">Parcial</span>
                                    @else
                                        <span class="text-xs text-gray-400">Pendiente</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50">
                            <td colspan="5" class="px-3 py-2 text-right font-semibold text-gray-700">Total:</td>
                            <td class="px-3 py-2 text-right font-bold text-gray-900">
                                ${{ number_format($facturaCompra->detalles->sum(fn($d) => $d->precio_unitario * $d->cantidad_esperada), 2) }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>

    {{-- Acciones --}}
    <div class="mt-6 flex justify-end gap-3">
        <a wire:navigate href="{{ route('lista-recepciones') }}">
            <x-secondary-button>Volver al listado</x-secondary-button>
        </a>

        @if ($modoEdicion && $facturaCompra)
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" @click.outside="open = false"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-md border border-gray-300 hover:bg-gray-50 transition">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Descargar
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="open" x-transition
                     class="absolute right-0 mt-1 w-44 bg-white rounded-md shadow-lg border border-gray-200 z-10">
                    <a href="{{ route('recepcion.pdf', $facturaCompra->id) }}" target="_blank"
                       class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-t-md">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Descargar PDF
                    </a>
                    <a href="{{ route('recepcion.excel', $facturaCompra->id) }}"
                       class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-b-md border-t border-gray-100">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18M10 3v18M14 3v18M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z" />
                        </svg>
                        Descargar Excel
                    </a>
                </div>
            </div>
        @endif

        @if (!$modoEdicion)
            <x-button wire:click="guardarFactura">
                Guardar Factura
            </x-button>
        @elseif ($facturaCompra && $facturaCompra->estado !== 'recibida')
            <x-button wire:click="abrirConfirmarRecepcion" class="bg-emerald-600 hover:bg-emerald-700">
                Confirmar Recepción
            </x-button>
        @endif
    </div>

</div>
