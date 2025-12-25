@props(['on'])

<div x-data="{ shown: false, timeout: null }"
    x-init="@this.on('{{ $on }}', () => { clearTimeout(timeout); shown = true; timeout = setTimeout(() => { shown = false }, 2000); })"
    x-show.transition.out.opacity.duration.1500ms="shown"
    x-transition:leave.opacity.duration.1500ms
    style=""
    {{ $attributes->merge(['class' => 'flex items-center justify-center text-sm font-medium text-green-800 bg-green-100 border border-green-200 mb-5 w-full h-12 rounded-lg shadow-sm']) }}>
    {{ $slot->isEmpty() ? 'Saved.' : $slot }}
</div>
