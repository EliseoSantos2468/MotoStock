<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Lista de Clientes') }}
        </h2>
    </x-slot>
    {{-- modales --}}
    <x-action-message class="mr-3" on="cliente-guardado">
        {{ __('¡Cliente guardado con éxito!') }}
    </x-action-message>
    <x-action-message class="mr-3" on="cliente-eliminado">
        {{ __('¡Cliente Eliminado con éxito!') }}
    </x-action-message>
    <x-action-message class="mr-3" on="cliente-editado">
        {{ __('¡Cliente Editado con éxito!') }}
    </x-action-message>
    {{-- modal cliente --}}
    <x-dialog-modal wire:model.live="modalCliente" maxWidth="4xl">
        @if ($form == 'crear')            
            <x-slot name="title">
                {{ __('Nuevo Cliente') }}
            </x-slot>
        @else
            <x-slot name="title">
                {{ __('Editar Cliente') }}
            </x-slot>
        @endif

        <x-slot name="content">
            <form id="form-{{$form}}-cliente" wire:submit.prevent="{{$form}}" class="mx-auto w-full max-w-4xl grid grid-cols-1 md:grid-cols-2 gap-4">

                <x-form-clientes 
                    :departamentos="$departamentos" 
                    :municipios="$municipios" 
                    :id_departamento="$id_departamento"
                    :id_municipio="$id_municipio"
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
                    Guardar Cliente
                </x-button>
            @else      
                <x-button wire:click="abrirConfirmacion" class="ml-3">
                    Editar Cliente
                </x-button>
            @endif

        </x-slot>

    </x-dialog-modal>
    {{-- fin modal cliente --}}

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
                    <x-button type="button" wire:click="{{$form}}" class="ml-3">
                        Si
                    </x-button>
                @else
                    <x-button type="button" wire:click="delete" class="ml-3">
                        Si
                    </x-button>
                @endif
            </x-slot>
        </x-confirmation-modal>
    {{-- fin modal confirmacion --}}



    {{-- fin modales --}}


    <div class="mb-5 flex justify-between">
        
        <div>
            <x-input type="text" placeholder="Buscar" wire:model.live="buscador"/>

            <select class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full sm:w-auto" 
                    name="filtro" 
                    wire:model.live="filtro">
                <option value="nombres_cliente" selected>Nombre</option>
                <option value="dui_cliente">DUI</option>
                <option value="id">ID</option>
            </select>
        </div>

        <x-btn-crear wire:click="crearCliente" class="w-full md:w-auto justify-center">
            Cliente
        </x-btn-crear>
    </div>

    <x-table>
        <x-slot name="thead">
            <x-th>ID</x-th>
            <x-th>Nombre</x-th>
            <x-th>DUI</x-th>
            <x-th>Clasificación</x-th>
            <x-th class="text-right">Acciones</x-th>
        </x-slot>

        @foreach ($clientes as $cliente)
            <x-tr>
                <x-td class="text-sm text-gray-900">{{ $cliente->id }}</x-td>
                <x-td class="text-sm font-medium text-gray-900">{{ $cliente->nombres_cliente }}</x-td>
                <x-td class="text-sm text-gray-500">{{ $cliente->dui_cliente }}</x-td>
                <x-td>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                        {{ $cliente->clasificacion->nombre_clasificacion ?? 'Sin Clasificar' }}
                    </span>
                </x-td>
                <x-td class="flex justify-end gap-2 text-right text-sm font-medium ">
                    <x-btn-editar wire:click="editarCliente({{$cliente->id}})" />
                    <x-btn-Eliminar wire:click="eliminarCliente({{$cliente->id}})" />
                    <x-btn-Ver wire:click="show({{$cliente->id}})" />
                </x-td>
            </x-tr>
        @endforeach
    </x-table>

    <div class="mt-4">
        {{ $clientes->links() }}
    </div>
</div>