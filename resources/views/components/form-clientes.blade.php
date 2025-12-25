@props(['departamentos' => [], 'municipios' => []])

<div>
    <x-label for="nombres" value="Nombres" />
    <x-input id="nombres" type="text" class="mt-1 block w-full" placeholder="ingrese nombres" wire:model="nombres_cliente" />
</div>

<div>
    <x-label for="apellidos" value="Apellidos" />
    <x-input id="apellidos" type="text" class="mt-1 block w-full" placeholder="ingrese apellidos" wire:model="apellidos_cliente" />
</div>

<div>
    <x-label for="dui" value="DUI" />
    <x-input id="dui" type="text" class="mt-1 block w-full" placeholder="00000000-0" wire:model="dui_cliente" />
</div>

<div>
    <x-label for="telefono" value="Telefono" />
    <x-input id="telefono" type="number" class="mt-1 block w-full" placeholder="7777-7777" wire:model="telefono_cliente" />
</div>

<div>
    <x-label for="nit" value="Nit" />
    <x-input id="nit" type="number" class="mt-1 block w-full" placeholder="ingrese nit" wire:model="nit_cliente" />
</div>

<div>
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
            <option value="{{$departamento->id}}">{{$departamento->nombre_departamento}}</option>
        @endforeach
    </select>
</div>

<div>
    <x-label for="municipio" value="Municipio" />
    <select name="" id="municipio" wire:model="id_municipio" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
        <option value="" selected disabled>Seleccione un municipio</option>
        @foreach ($municipios as $municipio)
            <option value="{{$municipio->id}}">{{$municipio->nombre_municipio}}</option>
        @endforeach
    </select>
</div>