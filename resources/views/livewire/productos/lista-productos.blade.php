<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    {{-- Mensajes de acción --}}
    <x-action-message class="mr-3" on="producto-eliminado">
        {{ __('Producto Eliminado con éxito!') }}
    </x-action-message>
    <x-action-message class="mr-3" on="producto-creado">
        {{ __('Producto Creado con éxito!') }}
    </x-action-message>
    <x-action-message class="mr-3" on="producto-actualizado">
        {{ __('Producto Actualizado con éxito!') }}
    </x-action-message>

    {{-- Modal Producto --}}
    <x-dialog-modal wire:model.live="modalProducto">

        <x-slot name="title">
            {{ $form === 'crear' ? __('Nuevo Producto') : __('Editar Producto') }}
        </x-slot>

        <x-slot name="content">
            <form id="form-{{$form}}-producto"
                  wire:submit="{{$form}}"
                  novalidate
                  class="mx-auto w-full max-w-5xl grid grid-cols-1 md:grid-cols-2 gap-4">

                <x-form-productos
                    :form="$form"
                    :marcas="$marcas"
                    :marcas_nuevas="$marcas_nuevas"
                    :precio-costo="$PrecioCosto"
                    :porcentaje-publico="$PorcentajePublico"
                    :porcentaje-mayoreo="$PorcentajeMayoreo"
                    :porcentaje-taller="$PorcentajeTaller"
                    :cantidad-mayoreo="$cantidadMayoreo"
                />

            </form>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="cerrarModal">
                Cancelar
            </x-secondary-button>

            <x-button wire:click="abrirConfirmacion" class="ml-3">
                {{ $form === 'crear' ? 'Guardar Producto' : 'Editar Producto' }}
            </x-button>
        </x-slot>

    </x-dialog-modal>
    {{-- Fin Modal Producto --}}

    {{-- Modal Confirmación --}}
    <x-confirmation-modal wire:model.live="modalConfirm">
        <x-slot name="title">{{ $modalConfirmTitle }}</x-slot>
        <x-slot name="content">{{ $modalConfirmContent }}</x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="cerrarConfirmacion">No</x-secondary-button>

            @if ($form)
                <x-button type="submit" form="form-{{$form}}-producto" class="ml-3">Sí</x-button>
            @else
                <x-button wire:click="delete" class="ml-3">Sí</x-button>
            @endif
        </x-slot>
    </x-confirmation-modal>
    {{-- Fin Modal Confirmación --}}

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kardex e Inventario') }}
        </h2>
    </x-slot>

    {{-- Buscador y botón crear --}}
    <div class="mb-5 flex flex-col gap-4 md:flex-row md:justify-between md:items-center">
        <div class="flex flex-col sm:flex-row gap-2">
            <x-input type="text"
                     placeholder="Buscar"
                     wire:model.live.debounce.400ms="buscador"
                     class="w-full sm:w-64"/>

            <select class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full sm:w-auto"
                    wire:model.live="filtro">
                <option value="nombre_producto" selected>Nombre</option>
                <option value="id">ID</option>
            </select>
        </div>

        <x-btn-crear wire:click="crearProducto" class="w-full md:w-auto justify-center">
            Producto
        </x-btn-crear>
    </div>

    {{-- Tabla --}}
    <x-table>
        <x-slot name="thead">
            <x-th>ID</x-th>
            <x-th>Nombre</x-th>
            <x-th>Marcas</x-th>
            <x-th>P. Costo</x-th>
            <x-th>% Ganancia</x-th>
            <x-th>% Descuento</x-th>
            <x-th>P. Publico</x-th>
            <x-th>P. Mayoreo</x-th>
            <x-th>P. Taller</x-th>
            <x-th>Mín. Mayoreo</x-th>  {{-- ← NUEVO --}}
            <x-th>Cantidad</x-th>
            <x-th class="text-right">Acciones</x-th>
        </x-slot>

        @foreach ($productos as $producto)
            <x-tr>
                <x-td class="text-sm text-gray-900">{{ $producto->id }}</x-td>
                <x-td class="text-sm font-medium text-gray-900">{{ $producto->nombre_producto }}</x-td>

                <x-td class="text-sm text-gray-500">
                    @foreach ($producto->marcas as $marca)
                        <div class="whitespace-nowrap">{{ $marca->nombre_marca }}</div>
                    @endforeach
                </x-td>

                <x-td class="text-sm text-gray-500">
                    @foreach ($producto->marcas as $marca)
                        <div class="whitespace-nowrap">${{ number_format($marca->pivot->precio_costo ?? 0, 2) }}</div>
                    @endforeach
                </x-td>

                <x-td class="text-xs text-gray-500">
                    @foreach ($producto->marcas as $marca)
                        <div class="whitespace-nowrap space-y-1">
                            @php
                                $porcentajeP = $marca->pivot->porcentaje_publico ?? 0;
                                $porcentajeM = $marca->pivot->porcentaje_mayoreo ?? 0;
                                $porcentajeT = $marca->pivot->porcentaje_taller ?? 0;
                                
                                $colorP = $porcentajeP >= 25 ? 'bg-green-100 text-green-700' : ($porcentajeP >= 15 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700');
                                $colorM = $porcentajeM >= 25 ? 'bg-green-100 text-green-700' : ($porcentajeM >= 15 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700');
                                $colorT = $porcentajeT >= 25 ? 'bg-green-100 text-green-700' : ($porcentajeT >= 15 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700');
                            @endphp
                            <div class="flex gap-1 flex-wrap">
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold {{ $colorP }}">P: {{ number_format($porcentajeP, 2) }}%</span>
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold {{ $colorM }}">M: {{ number_format($porcentajeM, 2) }}%</span>
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold {{ $colorT }}">T: {{ number_format($porcentajeT, 2) }}%</span>
                            </div>
                        </div>
                    @endforeach
                </x-td>

                <x-td class="text-xs text-gray-500">
                    @foreach ($producto->marcas as $marca)
                        @php
                            $descuentoMayoreo = ($marca->pivot->precio_cliente ?? 0) > 0
                                ? ((($marca->pivot->precio_cliente ?? 0) - ($marca->pivot->precio_mayoreo ?? 0)) / ($marca->pivot->precio_cliente ?? 1)) * 100
                                : 0;
                            $descuentoTaller = ($marca->pivot->precio_cliente ?? 0) > 0
                                ? ((($marca->pivot->precio_cliente ?? 0) - ($marca->pivot->precio_taller ?? 0)) / ($marca->pivot->precio_cliente ?? 1)) * 100
                                : 0;
                            
                            $colorDescM = $descuentoMayoreo >= 20 ? 'bg-red-100 text-red-700' : ($descuentoMayoreo >= 10 ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700');
                            $colorDescT = $descuentoTaller >= 20 ? 'bg-red-100 text-red-700' : ($descuentoTaller >= 10 ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700');
                        @endphp
                        <div class="whitespace-nowrap space-y-1">
                            <div class="flex gap-1">
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold {{ $colorDescM }}">M: {{ number_format($descuentoMayoreo, 2) }}%</span>
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold {{ $colorDescT }}">T: {{ number_format($descuentoTaller, 2) }}%</span>
                            </div>
                        </div>
                    @endforeach
                </x-td>

                <x-td class="text-sm text-gray-500">
                    @foreach ($producto->marcas as $marca)
                        <div class="whitespace-nowrap">${{ number_format($marca->pivot->precio_cliente, 2) }}</div>
                    @endforeach
                </x-td>

                <x-td class="text-sm text-gray-500">
                    @foreach ($producto->marcas as $marca)
                        <div class="whitespace-nowrap">${{ number_format($marca->pivot->precio_mayoreo, 2) }}</div>
                    @endforeach
                </x-td>

                {{-- ← NUEVA COLUMNA --}}
                <x-td class="text-sm text-gray-500">
                    @foreach ($producto->marcas as $marca)
                        <div class="whitespace-nowrap">${{ number_format($marca->pivot->precio_taller ?? 0, 2) }}</div>
                    @endforeach
                </x-td>

                <x-td class="text-sm text-gray-500">
                    @foreach ($producto->marcas as $marca)
                        <div class="whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">
                                ≥ {{ $marca->pivot->cantidad_mayoreo }} uds.
                            </span>
                        </div>
                    @endforeach
                </x-td>

                <x-td class="text-sm text-gray-500">
                    @foreach ($producto->marcas as $marca)
                        <div class="font-bold text-indigo-600">{{ $marca->pivot->cantidad }}</div>
                    @endforeach
                </x-td>

                <x-td class="flex justify-end gap-2 text-right text-sm font-medium">
                    <x-btn-editar wire:click="editarProducto({{ $producto->id }})" />
                    <x-btn-eliminar wire:click="eliminarProducto({{ $producto->id }})" />
                    <x-btn-ver wire:click="show({{ $producto->id }})" />
                </x-td>
            </x-tr>
        @endforeach
    </x-table>

    <div class="mt-4">
        {{ $productos->links() }}
    </div>

</div>