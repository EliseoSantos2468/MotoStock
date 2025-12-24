<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Lista de Clientes') }}
        </h2>
    </x-slot>

    <div class="mb-5 flex justify-between">
        
        <div>
            <x-input type="text" placeholder="Buscar" wire:model.live="buscador"/>

            <select class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                    name="filtro" 
                    wire:model.live="filtro">

                <option value="nombres_cliente" selected>Nombre</option>
                <option value="dui_cliente">DUI</option>
                <option value="id">ID</option>

            </select>
        </div>

        <x-btn-crear>
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
                    <x-btn-editar wire:click="" />
                    <x-btn-Eliminar wire:click="" />
                    <x-btn-Ver wire:click="" />
                </x-td>
            </x-tr>
        @endforeach
    </x-table>

    <div class="mt-4">
        {{ $clientes->links() }}
    </div>
</div>