@props(['for'])

@error($for)
    <p data-error-message {{ $attributes->merge(['class' => 'text-sm text-red-600']) }}>{{ $message }}</p>
@enderror
