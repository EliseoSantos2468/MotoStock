@props([
    'departamentos' => [],
    'municipios' => [],
    'referencias' => [],
    'id_departamento' => '',
    'id_municipio' => '',
    'form' => '',
    ])

<div>
    <x-label for="nombres" value="Nombres" />
    <x-input id="nombres" type="text" class="mt-1 block w-full" placeholder="ingrese nombres" wire:model="nombres_cliente" />
</div>

<div>
    <x-label for="apellidos" value="Apellidos" />
    <x-input id="apellidos" type="text" class="mt-1 block w-full" placeholder="ingrese apellidos" wire:model="apellidos_cliente" />
</div>

<div class="{{ $form == 'editar' ? 'hidden' : '' }}">
    <x-label for="dui" value="DUI" />
    <x-input id="dui" type="text" class="mt-1 block w-full" placeholder="00000000-0" wire:model="dui_cliente"/>
</div>

<div>
    <x-label for="telefono" value="Telefono" />
    <x-input id="telefono" type="text" class="mt-1 block w-full" placeholder="7777-7777" wire:model="telefono_cliente" />
</div>

<div class="{{ $form == 'editar' ? 'hidden' : '' }}">
    <x-label for="nit" value="Nit" />
    <x-input id="nit" type="number" class="mt-1 block w-full" placeholder="ingrese nit" wire:model="nit_cliente" />
</div>

<div class="{{ $form == 'editar' ? 'hidden' : '' }}">
    <x-label for="correo" value="Correo" />
    <x-input id="correo" type="email" class="mt-1 block w-full" placeholder="ingrese correo" wire:model="email_cliente" />
</div>

<div>
    <x-label for="barrio" value="Barrio" />
    <x-input id="barrio" type="text" class="mt-1 block w-full" placeholder="ingrese direccion" wire:model="barrio" />
</div>

<div>
    <x-label for="departamento" value="Departamento" />
    <select name="" id="departamento" wire:model.live="id_departamento" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
        <option value="" selected disabled>Seleccione un departamento</option>
        @foreach ($departamentos as $departamento)
            <option value="{{$departamento->id}}" {{$id_departamento == $departamento->id ? 'selected' : ''}}>
                {{$departamento->nombre_departamento}}
            </option>
        @endforeach
    </select>
</div>

<div class="md:col-span-2">
    <x-label for="municipio" value="Municipio" />
    <select name="" id="municipio" wire:model="id_municipio" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
        <option value="" selected disabled>Seleccione un municipio</option>
        @foreach ($municipios as $municipio)
            <option value="{{$municipio->id}}" {{$id_municipio == $municipio->id ? 'selected' : ''}}>
                {{$municipio->nombre_municipio}}
            </option>
        @endforeach
    </select>
</div>

<div class="md:col-span-2">
    <x-label for="referencias" value="Referencias" />
    <div id="referencias" class="flex gap-2 justify-between">
        <x-input wire:model="ref_nombre" type="text" name="" id="" placeholder="Nombre referencia" class="w-full"/>
        <x-input wire:model="ref_telefono" type="number" name="" id="" placeholder="Telefono referencia" class="w-full"/>
        <a href="" wire:click.prevent='agregarReferencia' class="bg-red-500 items-center text-white/80 hover:text-white rounded-full">
            <svg
            class="w-10 h-10"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1"
            stroke-linecap="round"
            stroke-linejoin="round"
            >
            <path d="M12 5l0 14" />
            <path d="M5 12l14 0" />
            </svg>
        </a>
    </div>
</div>

<div class="md:col-span-2 flex flex-col gap-2 ">
    @if ($referencias)
        @foreach ($referencias as $index => $referencia)
        <div class="w-full p-3 bg-slate-600 text-white rounded-md flex flex-row gap-3 items-center justify-between">
            <p>{{$index+1}}</p>
            <p><strong>Nombre: </strong>{{$referencia['nombre_ref']}}</p>
            <p><strong>Telefono: </strong>{{$referencia['telefono_ref']}}</p>
            <x-btn-eliminar wire:click='eliminarReferencia({{$index}})' />    
         </div>
        @endforeach
    @else
        <p class="w-full text-gray-600 font-bold text-center">No hay referencias disponibles</p>
    @endif
</div>