@props([
    'departamentos' => [],
    'municipios' => [],
    'id_departamento' => '',
    'id_municipio' => '',
    'form' => '',
])

<div class="w-full rounded-xl border border-slate-200 bg-slate-50/70 p-4 sm:p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5" 
        x-data="{
            maskDui(val) {
                return val.replace(/\D/g, '').replace(/^(\d{8})(\d)/, '$1-$2').substr(0, 10);
            },
            maskTel(val) {
                return val.replace(/\D/g, '').replace(/^(\d{4})(\d)/, '$1-$2').substr(0, 9);
            },
            filterName(val) {
                return val.replace(/[^a-zA-ZñÑáéíóúÁÉÍÓÚ\s]/g, '');
            }
        }">
        
        {{-- Nombres --}}
        <div class="md:col-span-1">
            <x-label for="nombres" value="Nombres" class="font-semibold" />
            <x-input id="nombres" type="text" class="mt-1 block w-full" placeholder="Nombres del cliente" 
                x-on:input="$el.value = filterName($el.value)" 
                wire:model="nombres_cliente" />
            <x-input-error for="nombres_cliente" class="mt-1" />
        </div>

        {{-- Apellidos --}}
        <div class="md:col-span-1">
            <x-label for="apellidos" value="Apellidos" class="font-semibold" />
            <x-input id="apellidos" type="text" class="mt-1 block w-full" placeholder="Apellidos del cliente" 
                x-on:input="$el.value = filterName($el.value)" 
                wire:model="apellidos_cliente" />
            <x-input-error for="apellidos_cliente" class="mt-1" />
        </div>

        {{-- DUI --}}
        <div class="md:col-span-1">
            <x-label for="dui" value="DUI" class="font-semibold" />
            <x-input id="dui" type="text" class="mt-1 block w-full" placeholder="00000000-0" 
                x-on:input="$el.value = maskDui($el.value)" 
                wire:model.blur="dui_cliente"/>
            <x-input-error for="dui_cliente" class="mt-1" />
        </div>

        {{-- Telefono --}}
        <div class="md:col-span-1">
            <x-label for="telefono" value="Teléfono" class="font-semibold" />
            <x-input id="telefono" type="text" class="mt-1 block w-full" placeholder="0000-0000" 
                x-on:input="$el.value = maskTel($el.value)" 
                wire:model.blur="telefono_cliente" />
            <x-input-error for="telefono_cliente" class="mt-1" />
        </div>

        {{-- Correo --}}
        <div class="md:col-span-1 {{ $form == 'editar' ? 'hidden' : '' }}">
            <x-label for="correo" value="Correo" class="font-semibold" />
            <x-input id="correo" type="email" class="mt-1 block w-full" placeholder="correo@ejemplo.com" 
                wire:model="email_cliente" />
            <x-input-error for="email_cliente" class="mt-1" />
        </div>

        {{-- Barrio --}}
        <div class="md:col-span-2">
            <x-label for="barrio" value="Barrio / Dirección Completa" class="font-semibold" />
            <x-input id="barrio" type="text" class="mt-1 block w-full" placeholder="Ej: Barrio El Centro, Ave. Independencia #12" 
                wire:model="barrio" />
            <x-input-error for="barrio" class="mt-1" />
        </div>

        {{-- Departamento --}}
        <div class="md:col-span-1">
            <x-label for="departamento" value="Departamento" class="font-semibold" />
            <select wire:model.live="id_departamento" class="mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                <option value="" selected disabled>Seleccione departamento</option>
                @foreach ($departamentos as $departamento)
                    <option value="{{$departamento->id}}">{{$departamento->nombre_departamento}}</option>
                @endforeach
            </select>
            <x-input-error for="id_departamento" class="mt-1" />
        </div>

        {{-- Municipio --}}
        <div class="md:col-span-1">
            <x-label for="municipio" value="Municipio" class="font-semibold" />
            <select wire:model="id_municipio" class="mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                <option value="" selected disabled>Seleccione municipio</option>
                @foreach ($municipios as $municipio)
                    <option value="{{$municipio->id}}">{{$municipio->nombre_municipio}}</option>
                @endforeach
            </select>
            <x-input-error for="id_municipio" class="mt-1" />
        </div>
    </div>
</div>