<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
    {{-- Botón para volver --}}
    <div class="mb-6">
        <a href="{{ route('lista-productos') }}" wire:navigate>
            <x-secondary-button>
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('Volver al Inventario') }}
            </x-secondary-button>
        </a>
    </div>

    {{-- Card Principal del Producto --}}
    <div class="bg-white shadow-xl rounded-lg overflow-hidden border border-gray-200">
        {{-- Encabezado con Nombre del Repuesto --}}
        <div class="bg-gradient-to-r from-slate-700 to-slate-900 px-6 py-4 flex justify-between items-center">
            <h2 class="text-2xl font-bold text-white flex items-center">
                <svg class="w-8 h-8 mr-3 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                {{ $producto->nombre_producto }}
            </h2>
            <span class="bg-white/20 text-white px-3 py-1 rounded-full text-sm font-semibold">
                ID: #{{ $producto->id }}
            </span>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-8">
            {{-- Columna 1: Datos técnicos --}}
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Información General</h3>
                <div>
                    <p class="text-sm text-gray-500 uppercase font-bold">Descripción Técnica</p>
                    <p class="text-gray-900 font-medium leading-relaxed">
                        {{ $producto->descripcion_producto ?? 'Sin descripción disponible.' }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 uppercase font-bold">Fecha de Registro</p>
                    <p class="text-gray-900">{{ $producto->created_at->format('d/m/Y') }}</p>
                </div>
            </div>

            {{-- Columna 2: Marcas y Compatibilidad --}}
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Marcas Asociadas</h3>
                <div class="flex flex-wrap gap-2">
                    @forelse ($producto->marcas as $marca)
                        <span class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium bg-blue-100 text-blue-800 border border-blue-200">
                            {{ $marca->nombre_marca }}
                        </span>
                    @empty
                        <p class="text-sm text-gray-500 italic">No hay marcas vinculadas.</p>
                    @endforelse
                </div>
            </div>

            {{-- Columna 3: Resumen de Stock Total --}}
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Estado de Inventario</h3>
                <div class="bg-emerald-50 p-4 rounded-lg border border-emerald-100 text-center">
                    <p class="text-xs text-emerald-600 uppercase font-extrabold mb-1">Existencias Totales</p>
                    <p class="text-4xl font-bold text-emerald-700">
                        {{ $producto->marcas->sum('pivot.cantidad') }}
                    </p>
                    <p class="text-xs text-emerald-500 mt-1">Unidades disponibles en sistema</p>
                </div>
            </div>
        </div>

        {{-- Sección de Precios Detallados por Marca --}}
        <div class="bg-gray-50 p-6 border-t border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Lista de Precios y Stock por Marca
            </h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($producto->marcas as $marca)
                    <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start mb-3">
                            <span class="font-bold text-gray-900 text-lg">{{ $marca->nombre_marca }}</span>
                            <span class="text-xs font-bold px-2 py-1 rounded {{ $marca->pivot->cantidad > 5 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                Stock: {{ $marca->pivot->cantidad }}
                            </span>
                        </div>
                        
                        <div class="space-y-2 border-t pt-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Precio Público:</span>
                                <span class="font-bold text-indigo-600">${{ number_format($marca->pivot->precio_cliente, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Precio Mayoreo:</span>
                                <span class="font-bold text-slate-700">${{ number_format($marca->pivot->precio_mayoreo, 2) }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>