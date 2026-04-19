<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
    {{-- Botón para volver --}}
    <div class="mb-6">
        <a href="{{ route('lista-clientes') }}" wire:navigate>
            <x-secondary-button>
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('Volver a la lista') }}
            </x-secondary-button>
        </a>
    </div>

    {{-- Card Principal del Cliente --}}
    <div class="bg-white shadow-xl rounded-lg overflow-hidden border border-gray-200">
        {{-- Encabezado con Nombre y Clasificación --}}
        <div class="bg-gradient-to-r from-indigo-600 to-blue-700 px-6 py-4 flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center">
            <h2 class="text-xl sm:text-2xl font-bold text-white flex items-center">
                <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                {{ $cliente->nombres_cliente }} {{ $cliente->apellidos_cliente }}
            </h2>
            <span class="self-start sm:self-auto px-3 py-1 rounded-full text-xs font-semibold bg-white text-indigo-700 uppercase tracking-wider">
                {{ $cliente->clasificacion->nombre_clasificacion ?? 'Sin Clasificación' }}
            </span>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-8">
            {{-- Columna 1: Datos Personales --}}
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Datos Personales</h3>
                <div>
                    <p class="text-sm text-gray-500 uppercase font-bold">DUI</p>
                    <p class="text-gray-900 font-medium">{{ $cliente->dui_cliente }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 uppercase font-bold">NIT</p>
                    <p class="text-gray-900 font-medium">{{ $cliente->nit_cliente }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 uppercase font-bold">Teléfono</p>
                    <p class="text-indigo-600 font-bold">{{ $cliente->telefono_cliente }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 uppercase font-bold">Email</p>
                    <p class="text-gray-900">{{ $cliente->email_cliente }}</p>
                </div>
            </div>

            {{-- Columna 2: Ubicación y Dirección --}}
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Ubicación</h3>
                <div>
                    <p class="text-sm text-gray-500 uppercase font-bold">Departamento</p>
                    <p class="text-gray-900">{{ $cliente->departamento->nombre_departamento ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 uppercase font-bold">Municipio</p>
                    <p class="text-gray-900">{{ $cliente->municipio->nombre_municipio }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 uppercase font-bold">Barrio / Dirección</p>
                    <p class="text-gray-900 italic">{{ $cliente->barrio ?? 'No especificado' }}</p>
                </div>
            </div>

            {{-- Columna 3: Información Financiera --}}
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Financiero</h3>
                <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-100">
                    <p class="text-xs text-indigo-500 uppercase font-extrabold mb-1">Monto Máximo de Crédito</p>
                    <p class="text-3xl font-bold text-indigo-700">
                        ${{ number_format($cliente->monto_max, 2) }}
                    </p>
                </div>
                
            </div>
        </div>

        {{-- Sección de Referencias Personales --}}
        <div class="bg-gray-50 p-6 border-t border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 005.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Referencias Personales
            </h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse ($cliente->referencias as $referencia)
                    <div class="bg-white p-4 rounded-md border border-gray-200 shadow-sm flex items-center">
                        <div class="bg-gray-100 p-2 rounded-full mr-4">
                            <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-gray-800">{{ $referencia->nombre_ref }}</p>
                            <p class="text-sm text-gray-600">{{ $referencia->telefono_ref }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 italic col-span-full">Este cliente no posee referencias registradas.</p>
                @endforelse
            </div>
        </div>
        
        {{-- Seccíon de Recibos --}}
        <div class="bg-gray-50 p-6 border-t border-gray-200">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Historial de Recibos
            </h3>

            @if($cliente->recibos && $cliente->recibos->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    
                    @foreach($cliente->recibos as $recibo)
                        <a href="{{ route('recibo.pdf', $recibo->id) }}" target="_blank" 
                        class="block bg-white border border-gray-200 rounded-lg p-4 hover:shadow-lg hover:border-blue-500 transition-all cursor-pointer group">
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="bg-red-100 p-2 rounded-lg text-red-600 mr-3 group-hover:bg-red-200 transition-colors">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800">Recibo #{{ $recibo->id }}</p>
                                        <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($recibo->fecha)->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-green-600">${{ number_format($recibo->total, 2) }}</p>
                                </div>
                            </div>
                        </a>
                    @endforeach

                </div>
            @else
                <div class="bg-gray-50 border-l-4 border-gray-400 p-4 rounded-md">
                    <p class="text-gray-500 text-sm">Este cliente aún no tiene recibos registrados en el sistema.</p>
                </div>
            @endif
        </div>
    </div>
</div>