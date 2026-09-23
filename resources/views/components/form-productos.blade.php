@props([
    "form" => '',
    "marcas" => [],
    "marcas_nuevas" => [],
    "marcaEditandoIndex" => null,
    "precioCosto" => 0,
    "porcentajePublico" => 0,
    "porcentajeMayoreo" => 0,
    "porcentajeTaller" => 0,
    "cantidadMayoreo" => 3,
])

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

<div
    class="col-span-2 border-2 border-black/30 rounded-xl p-4 sm:p-6"
    x-data="{
        costo: $wire.entangle('PrecioCosto'),
        pctPublico: $wire.entangle('PorcentajePublico'),
        pctMayoreo: $wire.entangle('PorcentajeMayoreo'),
        pctTaller: $wire.entangle('PorcentajeTaller'),
        cantidadMayoreo: $wire.entangle('cantidadMayoreo'),

        num(v) { const n = parseFloat(v); return isNaN(n) ? 0 : n; },
        precio(pct) { return Math.round((this.num(this.costo) * (1 + this.num(pct) / 100)) * 100) / 100; },
        get pPublico() { return this.precio(this.pctPublico); },
        get pMayoreo() { return this.precio(this.pctMayoreo); },
        get pTaller()  { return this.precio(this.pctTaller); },
        margen(precio)   { return this.num(precio) - this.num(this.costo); },
        descuento(precio) { return this.pPublico > 0 ? Math.round(((this.pPublico - precio) / this.pPublico) * 10000) / 100 : 0; },
        fmt(n) { return this.num(n).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); },

        get costoValido() { return this.num(this.costo) > 0; },
        get publicoOk() { return !this.costoValido || this.pPublico >= this.num(this.costo); },
        get mayoreoOk()  { return !this.costoValido || this.pMayoreo >= this.num(this.costo); },
        get tallerOk()   { return !this.costoValido || this.pTaller  >= this.num(this.costo); },
        get jerarquiaOk() { return this.num(this.pctPublico) >= this.num(this.pctTaller) && this.num(this.pctTaller) >= this.num(this.pctMayoreo); },
    }"
>
    <p class="mb-3 text-sm font-semibold text-gray-700">Marcas</p>
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

        <div class="md:col-span-2">
            <x-label for="precioCosto" value="Precio Costo" />
            <div class="relative mt-1">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">$</span>
                <x-input x-model.number="costo" name="PrecioCosto" step="0.01" min="0" class="w-full pl-6" type="number" id="precioCosto" />
            </div>
            <x-input-error for="PrecioCosto" class="mt-1" />
        </div>
    </div>

    {{-- Porcentajes de ganancia + resultado en vivo, lado a lado --}}
    <div class="mt-4 flex flex-col gap-4">

      <div class="flex flex-col lg:flex-row gap-4">

        <div class="space-y-3 lg:flex-1">
            <div>
                <div class="flex items-center justify-between gap-2">
                    <x-label for="porcentajePublico" value="% Ganancia Público" />
                    <span class="text-[11px] text-gray-400 whitespace-nowrap">≥ Taller ≥ Mayoreo</span>
                </div>
                <x-input x-model.number="pctPublico" name="PorcentajePublico" step="0.01" min="5" class="w-full" type="number" id="porcentajePublico" />
                <x-input-error for="PorcentajePublico" class="mt-1" />
            </div>

            <div>
                <x-label for="porcentajeTaller" value="% Ganancia Taller" />
                <x-input x-model.number="pctTaller" name="PorcentajeTaller" step="0.01" min="5" class="w-full" type="number" id="porcentajeTaller" />
                <x-input-error for="PorcentajeTaller" class="mt-1" />
            </div>

            <div>
                <x-label for="porcentajeMayoreo" value="% Ganancia Mayoreo" />
                <x-input x-model.number="pctMayoreo" name="PorcentajeMayoreo" step="0.01" min="5" class="w-full" type="number" id="porcentajeMayoreo" />
                <x-input-error for="PorcentajeMayoreo" class="mt-1" />
            </div>

            <p x-show="!jerarquiaOk" x-cloak class="text-xs font-semibold text-red-600">
                ❌ El orden debe ser Público ≥ Taller ≥ Mayoreo.
            </p>
        </div>

        <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-3 lg:flex-1">
            <p class="text-xs font-semibold uppercase text-emerald-700">Precios calculados (en vivo)</p>
            <div class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-3 text-sm">
                <div class="rounded bg-white p-2 border" :class="publicoOk ? 'border-emerald-100' : 'border-red-300'">
                    <p class="text-[11px] uppercase font-semibold text-gray-500">Público</p>
                    <p class="text-lg font-black" :class="publicoOk ? 'text-emerald-700' : 'text-red-700'" x-text="'$' + fmt(pPublico)"></p>
                    <p class="text-[10px] text-gray-500 mt-0.5" x-text="'Ganancia $' + fmt(margen(pPublico))"></p>
                    <p x-show="!publicoOk" x-cloak class="text-[10px] text-red-600 font-bold mt-1">❌ Menor al costo</p>
                </div>
                <div class="rounded bg-white p-2 border" :class="mayoreoOk ? 'border-emerald-100' : 'border-red-300'">
                    <p class="text-[11px] uppercase font-semibold text-gray-500">Mayoreo</p>
                    <p class="text-lg font-black" :class="mayoreoOk ? 'text-emerald-700' : 'text-red-700'" x-text="'$' + fmt(pMayoreo)"></p>
                    <p class="text-[10px] text-gray-500 mt-0.5" x-text="'-' + fmt(descuento(pMayoreo)) + '% vs público'"></p>
                    <p x-show="!mayoreoOk" x-cloak class="text-[10px] text-red-600 font-bold mt-1">❌ Menor al costo</p>
                </div>
                <div class="rounded bg-white p-2 border" :class="tallerOk ? 'border-emerald-100' : 'border-red-300'">
                    <p class="text-[11px] uppercase font-semibold text-gray-500">Taller</p>
                    <p class="text-lg font-black" :class="tallerOk ? 'text-emerald-700' : 'text-red-700'" x-text="'$' + fmt(pTaller)"></p>
                    <p class="text-[10px] text-gray-500 mt-0.5" x-text="'-' + fmt(descuento(pTaller)) + '% vs público'"></p>
                    <p x-show="!tallerOk" x-cloak class="text-[10px] text-red-600 font-bold mt-1">❌ Menor al costo</p>
                </div>
            </div>
        </div>

      </div>

        <div>
            <x-label for="cantidadMayoreo" value="Cantidad mínima para precio mayoreo" />
            <div class="flex flex-wrap items-center gap-3 mt-1">
                <x-input
                    x-model.number="cantidadMayoreo"
                    name="cantidadMayoreo"
                    class="w-40"
                    type="number"
                    min="1"
                    id="cantidadMayoreo"
                />
                <p class="text-sm text-gray-500 min-w-0">
                    Si el cliente compra
                    <span class="font-semibold text-indigo-600" x-text="cantidadMayoreo || 3"></span>
                    o más unidades, se aplicará el precio de mayoreo automáticamente.
                </p>
            </div>
            <x-input-error for="cantidadMayoreo" class="mt-1" />
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <x-button wire:click.prevent="agregarMarca" wire:loading.attr="disabled" wire:target="agregarMarca">
                <span wire:loading.remove wire:target="agregarMarca">{{ is_null($marcaEditandoIndex) ? 'Agregar Marca' : 'Actualizar Marca' }}</span>
                <span wire:loading wire:target="agregarMarca">Procesando...</span>
            </x-button>

            @if (!is_null($marcaEditandoIndex))
                <x-secondary-button wire:click.prevent="cancelarEdicionMarca">
                    Cancelar edición
                </x-secondary-button>
            @endif
        </div>

        <div>
            <x-input-error for="marcas_nuevas" class="mb-2" />

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
                            <x-td class="text-sm text-gray-900 align-top">
                                {{ $marcaN['idMarca'] }}
                            </x-td>

                            <x-td class="text-sm text-gray-900 uppercase align-top">
                                <button type="button" wire:click="cargarMarcaParaEdicion({{ $index }})" class="text-left font-semibold text-indigo-700 hover:text-indigo-900 hover:underline">
                                    {{ $marcaN['nombreMarca'] ?? 'Sin nombre' }}
                                </button>
                                <div class="text-[10px] text-gray-400">clic para editar</div>
                            </x-td>

                            <x-td class="text-sm text-indigo-600 font-bold align-top">
                                {{ $marcaN['cantidadMarca'] }}
                            </x-td>

                            <x-td class="text-sm text-gray-500 align-top">
                                ${{ number_format($marcaN['PrecioCosto'], 2) }}
                            </x-td>

                            <x-td class="text-xs text-gray-600 align-top">
                                <div>P: {{ number_format($marcaN['PorcentajePublico'], 2) }}%</div>
                                <div>M: {{ number_format($marcaN['PorcentajeMayoreo'], 2) }}%</div>
                                <div>T: {{ number_format($marcaN['PorcentajeTaller'], 2) }}%</div>
                            </x-td>

                            <x-td class="text-xs text-gray-600 align-top">
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

                            <x-td class="text-sm {{ $precioPublicoOk ? 'text-gray-500' : 'text-red-700 font-bold' }} align-top">
                                ${{ number_format($marcaN['PrecioC'], 2) }}
                                @if(!$precioPublicoOk)
                                <span class="text-[9px] block text-red-600">❌ Pérdida</span>
                                @endif
                            </x-td>

                            <x-td class="text-sm {{ $precioMayoreoOk ? 'text-gray-500' : 'text-red-700 font-bold' }} align-top">
                                ${{ number_format($marcaN['PrecioM'], 2) }}
                                @if(!$precioMayoreoOk)
                                <span class="text-[9px] block text-red-600">❌ Pérdida</span>
                                @endif
                            </x-td>

                            <x-td class="text-sm {{ $precioTallerOk ? 'text-gray-500' : 'text-red-700 font-bold' }} align-top">
                                ${{ number_format($marcaN['PrecioT'], 2) }}
                                @if(!$precioTallerOk)
                                <span class="text-[9px] block text-red-600">❌ Pérdida</span>
                                @endif
                            </x-td>

                            <x-td class="text-sm text-gray-500 align-top">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">
                                    ≥ {{ $marcaN['cantidadMayoreo'] ?? 3 }} uds.
                                </span>
                            </x-td>

                            <x-td class="text-right text-sm align-top">
                                <button type="button"
                                        wire:click="cargarMarcaParaEdicion({{ $index }})"
                                        class="text-indigo-600 hover:text-indigo-900 font-medium mr-3">
                                    Editar
                                </button>
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
</div>