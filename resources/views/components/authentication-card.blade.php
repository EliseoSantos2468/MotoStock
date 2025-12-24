<div class="min-h-screen flex sm:justify-end items-center pt-6 sm:pt-0 bg-gray-100">
    
    <div class="w-full h-screen flex flex-col justify-center items-center px-12 text-white bg-blue-500">
        <x-branding-panel />
    </div>

    <div class="w-full h-screen flex flex-col justify-center sm:max-w-md px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        {{$logo}}
        {{ $slot }}
    </div>
</div>
