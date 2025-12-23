<x-guest-layout>
    <x-authentication-card>

        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <h1 class="text-blue-600 font-bold text-center text-3xl">Iniciar Sesion</h1>
        <p class="text-gray-400 text-center text-xs">Ingresa tus credenciales para acceder al sistema</p>


        <x-validation-errors class="mb-4" />
        
        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-label for="email" value="{{ __('Correo') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Contraseña') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm text-gray-600">{{ __('Recordarme') }}</span>
                </label>
            </div>

            <div class="flex flex-col gap-2 items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                        {{ __('¿Olvidaste tu contraseña?') }}
                    </a>
                @endif

                <button class="
                        w-80 text-center 
                        text-white 
                        p-2 bg-gradient-to-r 
                        from-blue-600 
                        to-blue-800 rounded-md
                        hover:to-blue-600">
                    {{ __('Acceder') }}
                </button>
            </div>

            <div class="relative flex py-5 items-center">
                <div class="flex-grow border-t border-gray-300"></div>
                <span class="flex-shrink mx-4 text-gray-400 text-xs uppercase">o continuar con</span>
                <div class="flex-grow border-t border-gray-300"></div>
            </div>

            <div class="flex flex-row gap-10 justify-center">
                <a class="social-btn">
                    <span class="material-symbols-rounded">
                        <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="32"
                        height="32"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="#000000"
                        stroke-width="1"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        >
                        <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                        <path d="M3 7l9 6l9 -6" />
                        </svg>
                    </span>
                </a>
            </div>

            <div class="flex justify-center text-ms gap-2 text-gray-600 mt-5">
                ¿no tienes cuenta?
                <a class="text-blue-600 hover:text-blue-500 cursor-pointer" href="{{ route('register') }}">Registrate aqui</a> 
            </div>
        </form>
    </x-authentication-card> 
</x-guest-layout>
