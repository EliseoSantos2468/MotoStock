<div class="max-w-4xl mx-auto py-10 sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Ajustes del Sistema</h2>

        @if (session()->has('mensaje'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                {{ session('mensaje') }}
            </div>
        @endif

        <form wire:submit.prevent="confirmarGuardado" class="space-y-6">
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Correo para envío de Recibos</label>
                <input type="email" wire:model="correo_empresa" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                @error('correo_empresa') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Color Primario</label>
                    <div class="flex items-center space-x-3">
                        <input type="color" wire:model="color_primario" class="h-10 w-14 rounded cursor-pointer">
                        <span class="text-gray-500 text-sm font-mono uppercase">{{ $color_primario }}</span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Color Secundario</label>
                    <div class="flex items-center space-x-3">
                        <input type="color" wire:model="color_secundario" class="h-10 w-14 rounded cursor-pointer">
                        <span class="text-gray-500 text-sm font-mono uppercase">{{ $color_secundario }}</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between mt-8">
                <button type="button" wire:click="restaurarPorDefecto" class="text-sm text-gray-500 hover:text-gray-800 underline transition-colors">
                    Volver a los colores por defecto
                </button>

                <button type="submit" class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded transition-colors" style="background-color: {{ $primaryColor }}">
                    Guardar Cambios
                </button>
            </div>
        </form>

        <x-dialog-modal wire:model="modalConfirmacion">
            <x-slot name="title">
                Confirmar Configuración
            </x-slot>

            <x-slot name="content">
                ¿Estás seguro de que deseas aplicar esta nueva configuración? <br><br>
                La página se recargará automáticamente para aplicar los nuevos colores en todo el sistema.
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$set('modalConfirmacion', false)" class="mr-3 px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition-colors">
                    Cancelar
                </button>
                <button wire:click="guardarConfiguracion" class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-900 transition-colors" style="background-color: {{ $primaryColor }}">
                    Sí, aplicar cambios
                </button>
            </x-slot>
        </x-dialog-modal>

    </div>
</div>