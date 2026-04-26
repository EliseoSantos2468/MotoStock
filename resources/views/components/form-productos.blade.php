@props([
    "form" => '',
    "marcas" => [],
    "marcas_nuevas" => [],
])

<div class="col-span-2">
    <x-label for="nombre" value="Nombre" />
    <x-input id="nombre" name="nombre_producto" type="text" class="mt-1 block w-full" placeholder="ingrese el nombre del producto" wire:model="nombre_producto" />
    <x-input-error for="nombre_producto" class="mt-1" />
</div>

<div class="col-span-2">
    <x-label for="descripcion" value="Descripcion" />
    <textarea class="w-full" name="descripcion_producto" id="descripcion" wire:model="descripcion_producto"></textarea>
    <x-input-error for="descripcion_producto" class="mt-1" />
</div>

<fieldset class="col-span-2 border-2 border-black/30 rounded-xl p-4 sm:p-6">
    <legend>Marcas</legend>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

        <div>
            <x-label for="marca" value="Marca" />
            <select id="marca" name="idMarca" class="rounded-md cursor-pointer w-full" wire:model="idMarca">
                <option value="" selected disabled>Seleccione una Marca</option>
                @foreach ($marcas as $marca)
                    <option value="{{ $marca->id }}">{{ $marca->nombre_marca }}</option>
                @endforeach
            </select>
            <x-input-error for="idMarca" class="mt-1" />
        </div>

        <div>
            <x-label for="cantidad" value="Cantidad" />
            <x-input wire:model="cantidadMarca" name="cantidadMarca" class="w-full" type="number" min="1" id="cantidad" />
            <x-input-error for="cantidadMarca" class="mt-1" />
        </div>

        <div>
            <x-label for="precioC" value="Precio Cliente" />
            <x-input wire:model="PrecioC" name="PrecioC" step="0.01" min="0" class="w-full" type="number" id="precioC" />
            <x-input-error for="PrecioC" class="mt-1" />
        </div>

        <div>
            <x-label for="precioM" value="Precio Mayoreo" />
            <x-input wire:model="PrecioM" name="PrecioM" step="0.01" min="0" class="w-full" type="number" id="precioM" />
            <x-input-error for="PrecioM" class="mt-1" />
        </div>

        <div class="md:col-span-2">
            <x-label for="cantidadMayoreo" value="Cantidad mínima para precio mayoreo" />
            <div class="flex items-center gap-3 mt-1">
                <x-input
                    wire:model="cantidadMayoreo"
                    name="cantidadMayoreo"
                    class="w-40"
                    type="number"
                    min="1"
                    id="cantidadMayoreo"
                />
                <p class="text-sm text-gray-500">
                    Si el cliente compra 
                    <span class="font-semibold text-indigo-600" x-text="$wire.cantidadMayoreo || 3"></span> 
                    o más unidades, se aplicará el precio de mayoreo automáticamente.
                </p>
            </div>
            <x-input-error for="cantidadMayoreo" class="mt-1" />
        </div>

        <div class="md:col-span-2">
            <x-button wire:click.prevent="agregarMarca">
                Agregar Marca
            </x-button>
        </div>

        <div class="col-span-2">
            <x-table>
                <x-slot name="thead">
                    <x-th>Id</x-th>
                    <x-th>Nombre</x-th>
                    <x-th>Cantidad</x-th>
                    <x-th>P. Cliente</x-th>
                    <x-th>P. Mayoreo</x-th>
                    <x-th>Mín. Mayoreo</x-th>
                    <x-th class="text-right">Acciones</x-th>
                </x-slot>

                @if (!empty($marcas_nuevas))
                    @foreach ($marcas_nuevas as $index => $marcaN)
                        <x-tr>
                            <x-td class="text-sm text-gray-900">
                                {{ $marcaN['idMarca'] }}
                            </x-td>

                            <x-td class="text-sm text-gray-900 uppercase">
                                {{ $marcaN['nombreMarca'] ?? 'Sin nombre' }}
                            </x-td>

                            <x-td class="text-sm text-indigo-600 font-bold">
                                {{ $marcaN['cantidadMarca'] }}
                            </x-td>

                            <x-td class="text-sm text-gray-500">
                                ${{ number_format($marcaN['PrecioC'], 2) }}
                            </x-td>

                            <x-td class="text-sm text-gray-500">
                                ${{ number_format($marcaN['PrecioM'], 2) }}
                            </x-td>

                            <x-td class="text-sm text-gray-500">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">
                                    ≥ {{ $marcaN['cantidadMayoreo'] ?? 3 }} uds.
                                </span>
                            </x-td>

                            <x-td class="text-right text-sm">
                                <button type="button"
                                        wire:click="quitarMarca({{ $index }})"
                                        class="text-red-600 hover:text-red-900 font-medium">
                                    Quitar
                                </button>
                            </x-td>
                        </x-tr>
                    @endforeach
                @else
                    <x-tr>
                        <x-td colspan="7" class="py-8 text-center text-gray-500 italic">
                            No hay marcas agregadas en este producto
                        </x-td>
                    </x-tr>
                @endif
            </x-table>
        </div>

    </div>
</fieldset>