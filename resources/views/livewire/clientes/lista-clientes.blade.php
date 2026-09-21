<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
            <form id="form-{{$form}}-cliente" wire:submit.prevent="abrirConfirmacion" class="mx-auto w-full max-w-4xl grid grid-cols-1 gap-4">
                <div wire:loading.flex wire:target="abrirConfirmacion,crear,editar" class="col-span-full items-center gap-3 rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-700">
                    <svg class="h-5 w-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    Procesando validación o guardado...
                </div>

                @if (session()->has('error'))
                    <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="col-span-full rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        <p class="font-semibold mb-2">Corrige los siguientes campos:</p>
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

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
            <x-secondary-button type="button" wire:click="cerrarModal" class="w-full sm:w-auto justify-center">
                Cancelar
            </x-secondary-button>
            
            @if ($form == 'crear')            
                <x-button type="submit" form="form-{{$form}}-cliente" wire:loading.attr="disabled" wire:target="abrirConfirmacion" class="w-full sm:w-auto sm:ml-3 justify-center">
                    <span wire:loading.remove wire:target="abrirConfirmacion">Guardar Cliente</span>
                    <span wire:loading wire:target="abrirConfirmacion" class="inline-flex items-center gap-2">
                        <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        Verificando...
                    </span>
                </x-button>
            @else      
                <x-button type="submit" form="form-{{$form}}-cliente" wire:loading.attr="disabled" wire:target="abrirConfirmacion" class="w-full sm:w-auto sm:ml-3 justify-center">
                    <span wire:loading.remove wire:target="abrirConfirmacion">Editar Cliente</span>
                    <span wire:loading wire:target="abrirConfirmacion" class="inline-flex items-center gap-2">
                        <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        Verificando...
                    </span>
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
                <x-secondary-button type="button" wire:click="cerrarConfirmacion" wire:loading.attr="disabled" wire:target="crear,editar">
                    No
                </x-secondary-button>

                @if ($form)                    
                    <x-button type="button" wire:click="{{$form}}" wire:loading.attr="disabled" wire:target="{{$form}}" class="ml-3">
                        <span wire:loading.remove wire:target="{{$form}}">Si</span>
                        <span wire:loading wire:target="{{$form}}" class="inline-flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            Guardando...
                        </span>
                    </x-button>
                @else
                    <x-button type="button" wire:click="delete" wire:loading.attr="disabled" wire:target="delete" class="ml-3">
                        <span wire:loading.remove wire:target="delete">Si</span>
                        <span wire:loading wire:target="delete" class="inline-flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            Eliminando...
                        </span>
                    </x-button>
                @endif
            </x-slot>
        </x-confirmation-modal>
    {{-- fin modal confirmacion --}}



    {{-- fin modales --}}


    <div class="mb-5 flex flex-col gap-4 md:flex-row md:justify-between md:items-center">
        
        <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
            <x-input type="text" placeholder="Buscar" wire:model.live.debounce.400ms="buscador" class="w-full sm:w-64"/>

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
                    <x-btn-eliminar wire:click="eliminarCliente({{$cliente->id}})" />
                    <x-btn-ver wire:click="show({{$cliente->id}})" />
                </x-td>
            </x-tr>
        @endforeach
    </x-table>

    <div class="mt-4">
        {{ $clientes->links() }}
    </div>
</div>