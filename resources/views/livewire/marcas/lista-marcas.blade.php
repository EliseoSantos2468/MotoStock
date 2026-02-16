<div>
    {{-- modales --}}
    <x-action-message class="mr-3" on="marca-eliminada">
    {{ __('Marca Eliminada con éxito!') }}
    </x-action-message>
    <x-action-message class="mr-3" on="marca-guardada">
        {{ __('Marca Creada con éxito!') }}
    </x-action-message>
    <x-action-message class="mr-3" on="marca-editada">
        {{ __('Marca Editada con éxito!') }}
    </x-action-message>

    {{-- modal Marca --}}
    <x-dialog-modal wire:model.live="modalMarca">
        @if ($form == 'crear')            
            <x-slot name="title">
                {{ __('Nueva Marca') }}
            </x-slot>
        @else
            <x-slot name="title">
                {{ __('Editar Marca') }}
            </x-slot>
        @endif

        <x-slot name="content">
            <form id="form-{{$form}}-marca" wire:submit="{{$form}}" class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <x-form-marcas 
                    :form="$form"
                    />
            </form>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="cerrarModal">
                Cancelar
            </x-secondary-button>
            
            @if ($form == 'crear')            
                <x-button wire:click="abrirConfirmacion" class="ml-3">
                    Guardar Marca
                </x-button>
            @else      
                <x-button wire:click="abrirConfirmacion" class="ml-3">
                    Editar Marca
                </x-button>
            @endif

        </x-slot>

    </x-dialog-modal>
    {{-- fin modal Marca --}}

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
                    <x-button type="submit" form="form-{{$form}}-marca" class="ml-3">
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
            {{ __('Lista de Marcas') }}
        </h2>
    </x-slot>

    <div class="mb-5 flex flex-col gap-4 md:flex-row md:justify-between md:items-center">
        
        <div class="flex flex-col sm:flex-row gap-2">
            <x-input type="text" placeholder="Buscar" wire:model.live="buscador" class="w-full sm:w-64"/>

            <select class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full sm:w-auto" 
                    name="filtro" 
                    wire:model.live="filtro">
                <option value="nombre_marca" selected>Nombre</option>
                <option value="id">ID</option>
            </select>
        </div>

        <x-btn-crear wire:click="crearMarca" class="w-full md:w-auto justify-center">
            Marca
        </x-btn-crear>
    </div>

    <x-table>
        <x-slot name="thead">
            <x-th>ID</x-th>
            <x-th>Nombre</x-th>
            <x-th class="text-right">Acciones</x-th>
        </x-slot>

        @foreach ($marcas as $marca)
            <x-tr>
                <x-td class="text-sm text-gray-900">{{ $marca->id }}</x-td>
                <x-td class="text-sm font-medium text-gray-900">{{ $marca->nombre_marca }}</x-td>
                <x-td class="flex justify-end gap-2 text-right text-sm font-medium">
                        <x-btn-editar wire:click="editarMarca({{ $marca->id }})" />
                        <x-btn-Eliminar wire:click="eliminarMarca({{ $marca->id }})" />
                </x-td>
            </x-tr>
            @endforeach
    </x-table>

    <div class="mt-4">
        {{ $marcas->links() }}
    </div>

</div>
