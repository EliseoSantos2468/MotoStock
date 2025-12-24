<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <x-banner />

    <div class="min-h-screen bg-gray-100 grid grid-cols-[280px_1fr]">
        

        <x-side-bar />


        <div class="flex flex-col">
            
            @livewire('navigation-menu')

            <main class="p-8 flex-1">
                
                @if (isset($header))
                    <div class="mb-8">
                        {{ $header }}
                    </div>
                @endif

                <div class="bg-white shadow-sm rounded-lg p-6">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

        @stack('modals')

        @livewireScripts
    </body>
</html>
