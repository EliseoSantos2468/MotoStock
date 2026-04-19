@props([
    'form' => '',
    ])

<div>
    <x-label for="nombre" value="Nombre" />
    <x-input id="nombre" type="text" class="mt-1 block w-full" placeholder="ingrese el nombre de la marca" wire:model="nombre_marca" />
    <x-input-error for="nombre_marca" class="mt-1" />
</div>