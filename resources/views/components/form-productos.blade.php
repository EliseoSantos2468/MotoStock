@props([
    "form" => '',
    "marcas" => [],
    "marcas_nuevas" => [],
])

<div class="col-span-2">
    <x-label for="nombre" value="Nombre" />
    <x-input id="nombre" type="text" class="mt-1 block w-full" placeholder="ingrese el nombre del producto" wire:model="nombre_producto" />
</div>

<div class="col-span-2">
    <x-label for="descripcion" value="Descripcion" />
    <textarea class="w-full" name="" id="descripcion" wire:model="descripcion_producto"></textarea>
</div>

<fieldset class="col-span-2 border-2 border-black/30 rounded-xl p-6">
    <legend>Marcas</legend>
    <div class="grid grid-cols-2 gap-2">
        <div>
            <x-label for="marca" value="Marca" />
            <select name="" id="marca" class="rounded-md cursor-pointer w-full" wire:model="idMarca">
                <option value="" selected disabled>Seleccione una Marca</option>
                @foreach ($marcas as $marca)
                    <option value="{{$marca->id}}">{{$marca->nombre_marca}}</option>
                @endforeach
            </select>
        </div>
    
        <div>
            <x-label for="cantidad" value="Cantidad" />
            <x-input wire:model="cantidadMarca" class="w-full" type="number" name="" id="cantidad" />
        </div>
    
        <div>
            <x-label for="precioC" value="Precio Cliente" />
            <x-input wire:model="PrecioC" step="0.01" min="0" class="w-full" type="number" name="" id="precioC" />
        </div>
    
        <div>
            <x-label for="precioM" value="Precio Mayoreo" />
            <x-input wire:model="PrecioM" step="0.01" min="0" class="w-full" type="number" name="" id="precioM" />
        </div>
    
        <div>
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
                    <x-th>Precio de Cliente</x-th>
                    <x-th>Precio de Mayoreo</x-th>
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
                        <x-td colspan="6" class="py-8 text-center text-gray-500 italic">
                            No hay marcas agregadas en este producto
                        </x-td>
                    </x-tr>
                @endif
            </x-table>
        </div>
    </div>
</fieldset>