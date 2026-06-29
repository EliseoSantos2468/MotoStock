<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <x-action-message class="mr-3" on="factura-eliminada">Factura eliminada con éxito.</x-action-message>

    {{-- Modal confirmación eliminar --}}
    <x-confirmation-modal wire:model.live="modalConfirm">
        <x-slot name="title">¿Eliminar factura?</x-slot>
        <x-slot name="content">Esta acción no se puede deshacer. ¿Desea eliminar la factura de compra?</x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('modalConfirm', false)">No</x-secondary-button>
            <x-danger-button wire:click="eliminar" class="ml-3">Sí, eliminar</x-danger-button>
        </x-slot>
    </x-confirmation-modal>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Recepción de Mercancía</h2>
    </x-slot>

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md text-sm">{{ session('error') }}</div>
    @endif

    {{-- Filtros --}}
    <div class="mb-5 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5">
        <x-input
            type="text"
            placeholder="N° de factura..."
            wire:model.live.debounce.400ms="buscador"
            class="w-full" />

        <select class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                wire:model.live="filtroProveedor">
            <option value="">Todos los proveedores</option>
            @foreach ($proveedores as $prov)
                <option value="{{ $prov->id }}">{{ $prov->nombre_proveedor }}</option>
            @endforeach
        </select>

        <select class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                wire:model.live="filtroEstado">
            <option value="">Todos los estados</option>
            <option value="pendiente">Pendiente</option>
            <option value="parcial">Parcial</option>
            <option value="recibida">Recibida</option>
        </select>

        <x-input type="date" wire:model.live="filtroFechaDesde" class="w-full" />
        <x-input type="date" wire:model.live="filtroFechaHasta" class="w-full" />
    </div>

    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center">
        <button wire:click="limpiarFiltros"
                class="text-sm text-indigo-600 hover:underline">
            Limpiar filtros
        </button>
        <a wire:navigate href="{{ route('nueva-recepcion') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Nueva Factura
        </a>
    </div>

    <x-table>
        <x-slot name="thead">
            <x-th>N° Factura</x-th>
            <x-th>Fecha</x-th>
            <x-th>Proveedor</x-th>
            <x-th>Estado</x-th>
            <x-th class="text-right">Acciones</x-th>
        </x-slot>

        @forelse ($facturas as $factura)
            <x-tr>
                <x-td class="text-sm font-medium text-gray-900">{{ $factura->numero_factura }}</x-td>
                <x-td class="text-sm text-gray-600">{{ $factura->fecha->format('d/m/Y') }}</x-td>
                <x-td class="text-sm text-gray-600">{{ $factura->proveedor->nombre_proveedor ?? '—' }}</x-td>
                <x-td>
                    @php
                        $badgeClass = match($factura->estado) {
                            'recibida'  => 'bg-emerald-100 text-emerald-700',
                            'parcial'   => 'bg-blue-100 text-blue-700',
                            default     => 'bg-amber-100 text-amber-700',
                        };
                        $badgeLabel = match($factura->estado) {
                            'recibida'  => 'Recibida',
                            'parcial'   => 'Parcial',
                            default     => 'Pendiente',
                        };
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                        {{ $badgeLabel }}
                    </span>
                </x-td>
                <x-td class="flex justify-end gap-2 items-center">
                    <a wire:navigate href="{{ route('ver-recepcion', $factura->id) }}"
                       class="inline-flex items-center px-2 py-1 bg-indigo-50 text-indigo-700 text-xs font-medium rounded hover:bg-indigo-100 transition">
                        Ver detalle
                    </a>

                    {{-- Dropdown descargar --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @click.outside="open = false"
                                class="inline-flex items-center gap-1 px-2 py-1 bg-gray-50 text-gray-600 text-xs font-medium rounded border border-gray-200 hover:bg-gray-100 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-transition
                             class="absolute right-0 mt-1 w-40 bg-white rounded-md shadow-lg border border-gray-200 z-20">
                            <a href="{{ route('recepcion.pdf', $factura->id) }}" target="_blank"
                               class="flex items-center gap-2 px-3 py-2 text-xs text-gray-700 hover:bg-gray-50 rounded-t-md">
                                <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                PDF
                            </a>
                            <a href="{{ route('recepcion.excel', $factura->id) }}"
                               class="flex items-center gap-2 px-3 py-2 text-xs text-gray-700 hover:bg-gray-50 rounded-b-md border-t border-gray-100">
                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18M10 3v18M14 3v18M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z" />
                                </svg>
                                Excel
                            </a>
                        </div>
                    </div>

                    @if ($factura->estado === 'pendiente')
                        <x-btn-eliminar wire:click="confirmarEliminar({{ $factura->id }})" />
                    @endif
                </x-td>
            </x-tr>
        @empty
            <x-tr>
                <x-td colspan="5" class="text-center text-gray-500 py-8">
                    No hay facturas de compra registradas.
                </x-td>
            </x-tr>
        @endforelse
    </x-table>

    <div class="mt-4">{{ $facturas->links() }}</div>

</div>
