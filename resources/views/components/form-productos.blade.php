@props([
    "form" => '',
    "marcas" => [],
    "marcas_nuevas" => [],
    "precioCosto" => 0,
    "porcentajePublico" => 0,
    "porcentajeMayoreo" => 0,
    "porcentajeTaller" => 0,
    "cantidadMayoreo" => 3,
])

@php
    $PrecioCosto = $precioCosto;
    $PorcentajePublico = $porcentajePublico;
    $PorcentajeMayoreo = $porcentajeMayoreo;
    $PorcentajeTaller = $porcentajeTaller;
@endphp

<div class="col-span-2">
    <x-label for="nombre" value="Nombre" />
    <x-input id="nombre" name="nombre_producto" type="text" class="mt-1 block w-full" placeholder="ingrese el nombre del producto" wire:model="nombre_producto" />
    <x-input-error for="nombre_producto" class="mt-1" />
</div>

<div class="col-span-2">
    <x-label for="descripcion" value="Descripcion" />
    <textarea class="w-full" name="descripcion_producto" id="descripcion" wire:model="descripcion_producto"></textarea>
    <x-input-error for="descripcion_producto" class="mt-1" />
</div>

<fieldset class="col-span-2 border-2 border-black/30 rounded-xl p-4 sm:p-6">
    <legend>Marcas</legend>
    @php
        $precioPublicoPreview = round(((float) ($PrecioCosto ?? 0)) * (1 + (((float) ($PorcentajePublico ?? 0)) / 100)), 2);
        $precioMayoreoPreview = round(((float) ($PrecioCosto ?? 0)) * (1 + (((float) ($PorcentajeMayoreo ?? 0)) / 100)), 2);
        $precioTallerPreview = round(((float) ($PrecioCosto ?? 0)) * (1 + (((float) ($PorcentajeTaller ?? 0)) / 100)), 2);

        $descuentoMayoreoPreview = $precioPublicoPreview > 0
            ? round((($precioPublicoPreview - $precioMayoreoPreview) / $precioPublicoPreview) * 100, 2)
            : 0;
        $descuentoTallerPreview = $precioPublicoPreview > 0
            ? round((($precioPublicoPreview - $precioTallerPreview) / $precioPublicoPreview) * 100, 2)
            : 0;
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

        <div>
            <x-label for="marca" value="Marca" />
            <select id="marca" name="idMarca" class="rounded-md cursor-pointer w-full" wire:model="idMarca">
                <option value="" selected disabled>Seleccione una Marca</option>
                @foreach ($marcas as $marca)
                    <option value="{{ $marca->id }}">{{ $marca->nombre_marca }}</option>
                @endforeach
            </select>
            <x-input-error for="idMarca" class="mt-1" />
        </div>

        <div>
            <x-label for="cantidad" value="Cantidad" />
            <x-input wire:model="cantidadMarca" name="cantidadMarca" class="w-full" type="number" min="1" id="cantidad" />
            <x-input-error for="cantidadMarca" class="mt-1" />
        </div>

        <div>
            <x-label for="precioCosto" value="Precio Costo" />
            <x-input wire:model="PrecioCosto" name="PrecioCosto" step="0.01" min="0" class="w-full" type="number" id="precioCosto" />
            <x-input-error for="PrecioCosto" class="mt-1" />
        </div>

        <div>
            <x-label for="porcentajePublico" value="% Ganancia Público (Mayor o igual a Mayoreo)" />
            <x-input wire:model="PorcentajePublico" name="PorcentajePublico" step="0.01" min="5" class="w-full" type="number" id="porcentajePublico" />
            <x-input-error for="PorcentajePublico" class="mt-1" />
        </div>

        <div>
            <x-label for="porcentajeMayoreo" value="% Ganancia Mayoreo (Mayor o igual a Taller)" />
            <x-input wire:model="PorcentajeMayoreo" name="PorcentajeMayoreo" step="0.01" min="5" class="w-full" type="number" id="porcentajeMayoreo" />
            <x-input-error for="PorcentajeMayoreo" class="mt-1" />
        </div>

        <div>
            <x-label for="porcentajeTaller" value="% Ganancia Taller (Menor o igual a Mayoreo)" />
            <x-input wire:model="PorcentajeTaller" name="PorcentajeTaller" step="0.01" min="5" class="w-full" type="number" id="porcentajeTaller" />
            <x-input-error for="PorcentajeTaller" class="mt-1" />
        </div>

        <div class="md:col-span-2 rounded-lg border border-emerald-200 bg-emerald-50 p-3">
            <p class="text-xs font-semibold uppercase text-emerald-700">Precios Calculados por Porcentaje</p>
            <div class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-3 text-sm">
                @php
                    $costoBienDefinido = (float) $PrecioCosto > 0;
                    $precioPublicoValido = $costoBienDefinido && $precioPublicoPreview >= (float) $PrecioCosto;
                    $precioMayoreoValido = $costoBienDefinido && $precioMayoreoPreview >= (float) $PrecioCosto;
                    $precioTallerValido = $costoBienDefinido && $precioTallerPreview >= (float) $PrecioCosto;
                @endphp
                <div class="rounded bg-white p-2 border {{ $precioPublicoValido ? 'border-emerald-100' : 'border-red-300' }}">
                    <p class="text-[11px] uppercase font-semibold text-gray-500">Precio Público</p>
                    <p class="text-lg font-black {{ $precioPublicoValido ? 'text-emerald-700' : 'text-red-700' }}">${{ number_format($precioPublicoPreview, 2) }}</p>
                    @if(!$precioPublicoValido && $costoBienDefinido)
                    <p class="text-[10px] text-red-600 font-bold mt-1">❌ Menor al costo</p>
                    @endif
                </div>
                <div class="rounded bg-white p-2 border {{ $precioMayoreoValido ? 'border-emerald-100' : 'border-red-300' }}">
                    <p class="text-[11px] uppercase font-semibold text-gray-500">Precio Mayoreo</p>
                    <p class="text-lg font-black {{ $precioMayoreoValido ? 'text-emerald-700' : 'text-red-700' }}">${{ number_format($precioMayoreoPreview, 2) }}</p>
                    @if(!$precioMayoreoValido && $costoBienDefinido)
                    <p class="text-[10px] text-red-600 font-bold mt-1">❌ Menor al costo</p>
                    @endif
                </div>
                <div class="rounded bg-white p-2 border {{ $precioTallerValido ? 'border-emerald-100' : 'border-red-300' }}">
                    <p class="text-[11px] uppercase font-semibold text-gray-500">Precio Taller</p>
                    <p class="text-lg font-black {{ $precioTallerValido ? 'text-emerald-700' : 'text-red-700' }}">${{ number_format($precioTallerPreview, 2) }}</p>
                    @if(!$precioTallerValido && $costoBienDefinido)
                    <p class="text-[10px] text-red-600 font-bold mt-1">❌ Menor al costo</p>
                    @endif
                </div>
            </div>
            <div class="mt-2 text-xs text-gray-600">
                <span class="font-semibold">Descuento vs Público:</span>
                Mayoreo {{ number_format($descuentoMayoreoPreview, 2) }}% |
                Taller {{ number_format($descuentoTallerPreview, 2) }}%
            </div>

        </div>

        <div class="md:col-span-2">
            <x-label for="cantidadMayoreo" value="Cantidad mínima para precio mayoreo" />
            <div class="flex items-center gap-3 mt-1">
                <x-input
                    wire:model="cantidadMayoreo"
                    name="cantidadMayoreo"
                    class="w-40"
                    type="number"
                    min="1"
                    id="cantidadMayoreo"
                />
                <p class="text-sm text-gray-500">
                    Si el cliente compra 
                    <span class="font-semibold text-indigo-600" x-text="$wire.cantidadMayoreo || 3"></span> 
                    o más unidades, se aplicará el precio de mayoreo automáticamente.
                </p>
            </div>
            <x-input-error for="cantidadMayoreo" class="mt-1" />
        </div>

        <div class="md:col-span-2">
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
                    <x-th>Costo</x-th>
                    <x-th>% Ganancia</x-th>
                    <x-th>% Descuento</x-th>
                    <x-th>P. Publico</x-th>
                    <x-th>P. Mayoreo</x-th>
                    <x-th>P. Taller</x-th>
                    <x-th>Mín. Mayoreo</x-th>
                    <x-th class="text-right">Acciones</x-th>
                </x-slot>

                @if (!empty($marcas_nuevas))
                    @foreach ($marcas_nuevas as $index => $marcaN)
                        @php
                            $precioPublicoMarca = $marcaN['PrecioC'];
                            $precioMayoreoMarca = $marcaN['PrecioM'];
                            $precioTallerMarca = $marcaN['PrecioT'];
                            $costMarca = $marcaN['PrecioCosto'];
                            
                            $precioPublicoOk = $precioPublicoMarca >= $costMarca;
                            $precioMayoreoOk = $precioMayoreoMarca >= $costMarca;
                            $precioTallerOk = $precioTallerMarca >= $costMarca;
                            
                            $margenBajo = ((float) $marcaN['PorcentajePublico'] < 5 || (float) $marcaN['PorcentajeMayoreo'] < 5 || (float) $marcaN['PorcentajeTaller'] < 5);
                            $rowClass = (!$precioPublicoOk || !$precioMayoreoOk || !$precioTallerOk) ? 'bg-red-50 border-l-4 border-red-500' : ($margenBajo ? 'bg-yellow-50 border-l-4 border-yellow-400' : '');
                        @endphp
                        <x-tr class="{{ $rowClass }}">
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
                                ${{ number_format($marcaN['PrecioCosto'], 2) }}
                            </x-td>

                            <x-td class="text-xs text-gray-600">
                                <div class="{{ (float) $marcaN['PorcentajePublico'] < 5 ? 'text-red-600 font-bold' : '' }}">P: {{ number_format($marcaN['PorcentajePublico'], 2) }}%</div>
                                <div class="{{ (float) $marcaN['PorcentajeMayoreo'] < 5 ? 'text-red-600 font-bold' : '' }}">M: {{ number_format($marcaN['PorcentajeMayoreo'], 2) }}%</div>
                                <div class="{{ (float) $marcaN['PorcentajeTaller'] < 5 ? 'text-red-600 font-bold' : '' }}">T: {{ number_format($marcaN['PorcentajeTaller'], 2) }}%</div>
                            </x-td>

                            <x-td class="text-xs text-gray-600">
                                @php
                                    $descuentoMayoreo = $marcaN['PrecioC'] > 0
                                        ? round((($marcaN['PrecioC'] - $marcaN['PrecioM']) / $marcaN['PrecioC']) * 100, 2)
                                        : 0;
                                    $descuentoTaller = $marcaN['PrecioC'] > 0
                                        ? round((($marcaN['PrecioC'] - $marcaN['PrecioT']) / $marcaN['PrecioC']) * 100, 2)
                                        : 0;
                                @endphp
                                <div>M: {{ number_format($descuentoMayoreo, 2) }}%</div>
                                <div>T: {{ number_format($descuentoTaller, 2) }}%</div>
                            </x-td>

                            <x-td class="text-sm {{ $precioPublicoOk ? 'text-gray-500' : 'text-red-700 font-bold' }}">
                                ${{ number_format($marcaN['PrecioC'], 2) }}
                                @if(!$precioPublicoOk)
                                <span class="text-[9px] block text-red-600">❌ Pérdida</span>
                                @endif
                            </x-td>

                            <x-td class="text-sm {{ $precioMayoreoOk ? 'text-gray-500' : 'text-red-700 font-bold' }}">
                                ${{ number_format($marcaN['PrecioM'], 2) }}
                                @if(!$precioMayoreoOk)
                                <span class="text-[9px] block text-red-600">❌ Pérdida</span>
                                @endif
                            </x-td>

                            <x-td class="text-sm {{ $precioTallerOk ? 'text-gray-500' : 'text-red-700 font-bold' }}">
                                ${{ number_format($marcaN['PrecioT'], 2) }}
                                @if(!$precioTallerOk)
                                <span class="text-[9px] block text-red-600">❌ Pérdida</span>
                                @endif
                            </x-td>

                            <x-td class="text-sm text-gray-500">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">
                                    ≥ {{ $marcaN['cantidadMayoreo'] ?? 3 }} uds.
                                </span>
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
                        <x-td colspan="11" class="py-8 text-center text-gray-500 italic">
                            No hay marcas agregadas en este producto
                        </x-td>
                    </x-tr>
                @endif
            </x-table>
        </div>

    </div>
</fieldset>