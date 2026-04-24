<div class="min-h-screen bg-gray-100 lg:grid lg:grid-cols-2">
    <div class="hidden lg:flex min-h-screen flex-col justify-center items-center px-12 text-white bg-blue-500">
        <x-branding-panel />
    </div>

    <div class="w-full min-h-screen flex items-center justify-center px-4 py-8 sm:px-6">
        <div class="w-full max-w-md bg-white shadow-md overflow-hidden rounded-xl p-6 sm:p-8">
        {{$logo}}
        {{ $slot }}
        </div>
    </div>
</div>
