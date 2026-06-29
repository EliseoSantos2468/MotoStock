<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <x-action-message class="mr-3" on="proveedor-guardado">Proveedor creado con éxito.</x-action-message>
    <x-action-message class="mr-3" on="proveedor-editado">Proveedor actualizado con éxito.</x-action-message>
    <x-action-message class="mr-3" on="proveedor-eliminado">Proveedor eliminado con éxito.</x-action-message>

    {{-- Modal formulario --}}
    <x-dialog-modal wire:model.live="modalProveedor">
        <x-slot name="title">{{ $form === 'crear' ? 'Nuevo Proveedor' : 'Editar Proveedor' }}</x-slot>

        <x-slot name="content">
            <form id="form-{{ $form }}-proveedor" wire:submit="{{ $form }}" class="grid grid-cols-1 gap-4">
                <div>
                    <x-label value="Nombre del proveedor *" />
                    <x-input wire:model="nombre_proveedor" class="w-full mt-1" placeholder="Nombre del proveedor" />
                    <x-input-error for="nombre_proveedor" class="mt-1" />
                </div>
                <div>
                    <x-label value="Teléfono" />
                    <div class="flex gap-2 mt-1">
                        <select wire:model.live="codigo_pais"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                            @foreach ($paises as $codigo => $nombre)
                                <option value="{{ $codigo }}">{{ $nombre }}</option>
                            @endforeach
                        </select>
                        <x-input
                            wire:model.live="telefono"
                            type="tel"
                            inputmode="numeric"
                            maxlength="{{ $telefonoMaxlength }}"
                            class="w-full"
                            placeholder="{{ $telefonoPlaceholder }}"
                            x-on:input="
                                const ca = ['HN','GT','SV','NI','CR','PA'];
                                const country = $wire.codigo_pais;
                                let v = $event.target.value;
                                if (ca.includes(country)) {
                                    let d = v.replace(/\D/g, '').slice(0, 8);
                                    v = d.length > 4 ? d.slice(0, 4) + '-' + d.slice(4) : d;
                                } else if (['MX','CO','VE','AR'].includes(country)) {
                                    v = v.replace(/[^0-9\-\s]/g, '').slice(0, 12);
                                } else if (['US','CA'].includes(country)) {
                                    v = v.replace(/[^0-9\-\s\(\)]/g, '').slice(0, 14);
                                } else {
                                    v = v.replace(/[^0-9\+\-\s\(\)\.]/g, '').slice(0, 20);
                                }
                                $event.target.value = v;
                            "
                        />
                    </div>
                    <p class="text-xs text-gray-400 mt-1">{{ $telefonoFormato }}</p>
                    <x-input-error for="telefono" class="mt-1" />
                </div>
                <div>
                    <x-label value="Correo electrónico" />
                    <x-input wire:model="email" type="email" class="w-full mt-1" placeholder="correo@dominio.com" />
                    <p class="text-xs text-gray-400 mt-1">Formato: usuario@dominio.com</p>
                    <x-input-error for="email" class="mt-1" />
                </div>
            </form>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="cerrarModal">Cancelar</x-secondary-button>
            <x-button wire:click="abrirConfirmacion" class="ml-3">
                {{ $form === 'crear' ? 'Guardar Proveedor' : 'Actualizar Proveedor' }}
            </x-button>
        </x-slot>
    </x-dialog-modal>

    {{-- Modal confirmación --}}
    <x-confirmation-modal wire:model.live="modalConfirm">
        <x-slot name="title">{{ $modalConfirmTitle }}</x-slot>
        <x-slot name="content">{{ $modalConfirmContent }}</x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="cerrarConfirmacion">No</x-secondary-button>
            @if ($form)
                <x-button type="submit" form="form-{{ $form }}-proveedor" class="ml-3">Sí</x-button>
            @else
                <x-button wire:click="delete" class="ml-3">Sí</x-button>
            @endif
        </x-slot>
    </x-confirmation-modal>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Proveedores</h2>
    </x-slot>

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md text-sm">{{ session('error') }}</div>
    @endif

    <div class="mb-5 flex flex-col gap-4 md:flex-row md:justify-between md:items-center">
        <x-input type="text" placeholder="Buscar proveedor..." wire:model.live.debounce.400ms="buscador" class="w-full sm:w-64" />
        <x-btn-crear wire:click="crearProveedor" class="w-full md:w-auto justify-center">Proveedor</x-btn-crear>
    </div>

    <x-table>
        <x-slot name="thead">
            <x-th>ID</x-th>
            <x-th>Nombre</x-th>
            <x-th>Teléfono</x-th>
            <x-th>Correo</x-th>
            <x-th class="text-right">Acciones</x-th>
        </x-slot>

        @forelse ($proveedores as $proveedor)
            <x-tr>
                <x-td class="text-sm text-gray-500">{{ $proveedor->id }}</x-td>
                <x-td class="text-sm font-medium text-gray-900">{{ $proveedor->nombre_proveedor }}</x-td>
                <x-td class="text-sm text-gray-600">{{ $proveedor->telefono ?? '—' }}</x-td>
                <x-td class="text-sm text-gray-600">{{ $proveedor->email ?? '—' }}</x-td>
                <x-td class="flex justify-end gap-2">
                    <x-btn-editar wire:click="editarProveedor({{ $proveedor->id }})" />
                    <x-btn-eliminar wire:click="eliminarProveedor({{ $proveedor->id }})" />
                </x-td>
            </x-tr>
        @empty
            <x-tr>
                <x-td colspan="5" class="text-center text-gray-500 py-6">No hay proveedores registrados.</x-td>
            </x-tr>
        @endforelse
    </x-table>

    <div class="mt-4">{{ $proveedores->links() }}</div>

</div>
