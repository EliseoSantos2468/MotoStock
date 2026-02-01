<div>
    {{-- modales --}}
    <x-action-message class="mr-3" on="producto-eliminado">
    {{ __('Producto Eliminado con éxito!') }}
    </x-action-message>
    <x-action-message class="mr-3" on="producto-creado">
    {{ __('Producto Creado con éxito!') }}
    </x-action-message>
    <x-action-message class="mr-3" on="producto-actualizado">
    {{ __('Producto Actualizado con éxito!') }}
    </x-action-message>

    {{-- modal Producto --}}
    <x-dialog-modal wire:model.live="modalProducto">
        @if ($form == 'crear')            
            <x-slot name="title">
                {{ __('Nuevo Producto') }}
            </x-slot>
        @else
            <x-slot name="title">
                {{ __('Editar Producto') }}
            </x-slot>
        @endif

        <x-slot name="content">
            <form id="form-{{$form}}-producto" wire:submit="{{$form}}" class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <x-form-productos 
                    :form="$form"
                    :marcas="$marcas"
                    :marcas_nuevas="$marcas_nuevas"
                    />

            </form>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="cerrarModal">
                Cancelar
            </x-secondary-button>
            
            @if ($form == 'crear')            
                <x-button wire:click="abrirConfirmacion" class="ml-3">
                    Guardar Producto
                </x-button>
            @else      
                <x-button wire:click="abrirConfirmacion" class="ml-3">
                    Editar Producto
                </x-button>
            @endif

        </x-slot>

    </x-dialog-modal>
    {{-- fin modal Producto --}}

    {{-- modal confirmacion --}}
        <x-confirmation-modal wire:model.live="modalConfirm">
            <x-slot name="title">
                {{$modalConfirmTitle}}
            </x-slot>

            <x-slot name="content">
                {{$modalConfirmContent}}
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="cerrarConfirmacion">
                    No
                </x-secondary-button>

                @if ($form)                    
                    <x-button type="submit" form="form-{{$form}}-producto" class="ml-3">
                        Si
                    </x-button>
                @else
                    <x-button type="submit" wire:click="delete" class="ml-3">
                        Si
                    </x-button>
                @endif
            </x-slot>
        </x-confirmation-modal>
    {{-- fin modal confirmacion --}}

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kardex e Inventario') }}
        </h2>
    </x-slot>

    <div class="mb-5 flex justify-between">
        
        <div>
            <x-input type="text" placeholder="Buscar" wire:model.live="buscador"/>

            <select class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                    name="filtro" 
                    wire:model.live="filtro">

                <option value="nombre_producto" selected>Nombre</option>
                <option value="id">ID</option>

            </select>
        </div>

        <x-btn-crear wire:click="crearProducto">
            Producto
        </x-btn-crear>
    </div>

    <x-table>
        <x-slot name="thead">
            <x-th>ID</x-th>
            <x-th>Nombre</x-th>
            <x-th>Marcas</x-th>
            <x-th>P. Cliente</x-th>
            <x-th>P. Mayoreo</x-th>
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
                        <div class="whitespace-nowrap">${{ number_format($marca->pivot->precio_cliente, 2) }}</div>
                    @endforeach
                </x-td>

                <x-td class="text-sm text-gray-500">
                    @foreach ($producto->marcas as $marca)
                        <div class="whitespace-nowrap">${{ number_format($marca->pivot->precio_mayoreo, 2) }}</div>
                    @endforeach
                </x-td>

                <x-td class="text-sm text-gray-500">
                    @foreach ($producto->marcas as $marca)
                        <div class="font-bold text-indigo-600">{{ $marca->pivot->cantidad }}</div>
                    @endforeach
                </x-td>

                <x-td class="flex justify-end gap-2 text-right text-sm font-medium">
                    <x-btn-editar wire:click="editarProducto({{ $producto->id }})" />
                    <x-btn-Eliminar wire:click="eliminarProducto({{ $producto->id }})" />
                    <x-btn-Ver wire:click="show({{ $producto->id }})" />
                </x-td>
            </x-tr>
        @endforeach
    </x-table>

    <div class="mt-4">
        {{ $productos->links() }}
    </div>

</div>
