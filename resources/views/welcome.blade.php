<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Bienvenido | {{ config('app.name', 'Sistema de Inventario') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                font-family: 'Instrument Sans', sans-serif;
                background-color: #fcfcfc;
                /* Fondo sutil con textura abstracta */
                background-image: radial-gradient(#e5e7eb 0.5px, transparent 0.5px);
                background-size: 24px 24px;
            }
            .fade-in {
                animation: fadeIn 0.8s ease-out;
            }
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }
        </style>
    </head>
    <body class="antialiased text-[#1e293b] flex items-center justify-center min-h-screen p-6">

        <main class="w-full max-w-2xl text-center fade-in">
            <div class="mb-12 flex justify-center">
                <div class="relative">
                    <div class="h-20 w-20 bg-slate-900 rounded-3xl rotate-12 absolute -inset-1 opacity-10 blur-xl"></div>
                    <div class="h-20 w-20 bg-white border border-slate-200 rounded-3xl flex items-center justify-center relative shadow-sm">
                        <svg class="w-10 h-10 text-slate-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <h2 class="text-sm font-bold uppercase tracking-[0.3em] text-slate-400 mb-4">Plataforma Operativa</h2>
            <h1 class="text-4xl md:text-5xl font-semibold tracking-tight text-slate-900 mb-6">
                Bienvenido a tu nuevo <br> <span class="text-slate-500 font-light italic">sistema de inventario.</span>
            </h1>
            
            <p class="text-lg text-slate-500 max-w-lg mx-auto mb-12 leading-relaxed">
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('login') }}" 
                   class="group relative inline-flex items-center justify-center px-8 py-4 font-semibold text-white transition-all duration-200 bg-slate-900 font-pj rounded-2xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 hover:bg-slate-800 active:scale-95 shadow-xl shadow-slate-200">
                    Acceder al Panel
                    <svg class="w-5 h-5 ml-2 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
            </div>

            <footer class="mt-24 pt-8 border-t border-slate-100">
                <div class="flex items-center justify-center gap-8">
                    <div class="flex items-center gap-2">
                    </div>
                    <span class="text-xs text-slate-300">v1.0</span>
                </div>
            </footer>
        </main>

    </body>
</html>