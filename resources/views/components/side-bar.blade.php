<aside id="main-sidebar" class="sidebar text-white min-h-screen transition-transform duration-300 fixed z-50 w-64 h-screen overflow-y-auto transform -translate-x-full md:relative md:translate-x-0 m-0" style="background-color: {{$primaryColor}}">
    
    <div class="flex justify-end pr-4 pt-4 md:hidden">
        <button id="close-sidebar-btn" class="text-gray-300 hover:text-white focus:outline-none">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <nav class="sidebar-nav py-4 md:mt-5">
        <ul class="nav-list primary-nav space-y-2">
            
            <li class="nav-item dropdown-container">
                <a href="{{ route('dashboard') }}" class="nav-link flex items-center px-4 py-3 hover:bg-gray-800 transition-colors">
                    <svg class="w-8 h-8 mr-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 4a1 1 0 0 1 1 -1h16a1 1 0 0 1 1 1v12a1 1 0 0 1 -1 1h-16a1 1 0 0 1 -1 -1v-12z" />
                        <path d="M3 13h18" /> <path d="M8 21h8" /> <path d="M10 17l-.5 4" /> <path d="M14 17l.5 4" />
                    </svg>
                    <span class="nav-label flex-1">Escritorio</span>
                </a>
                <ul class="dropdown-menu overflow-hidden transition-all duration-300 h-0" style="background-color: {{$secondaryColor}}">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link px-12 py-2 text-sm text-gray-300 hover:text-white">Ver Resumen</a>
                    </li>
                </ul>
            </li>

            <li class="nav-item dropdown-container">
                <a href="#" class="nav-link dropdown-toggler flex items-center px-4 py-3 hover:bg-gray-800 transition-colors">
                    <svg class="w-8 h-8 mr-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10 13a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                        <path d="M8 21v-1a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v1" />
                        <path d="M15 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                        <path d="M5 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                    </svg>
                    <span class="nav-label flex-1">Clientes</span>
                </a>
                <ul class="dropdown-menu overflow-hidden transition-all duration-300 h-0" style="background-color: {{$secondaryColor}}">
                    <li class="nav-item">
                        <a href="{{route('lista-clientes')}}" class="nav-link px-12 py-2 text-sm text-gray-300 hover:text-white">Lista de Clientes</a>
                    </li>
                </ul>
            </li>
        
            <li class="nav-item">
                <a href="{{route('ventas')}}" class="nav-link flex items-center px-4 py-3 hover:bg-gray-800">
                    <svg class="w-8 h-8 mr-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                        <path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                        <path d="M17 17h-11v-14h-2" /> <path d="M6 5l14 1l-1 7h-13" />
                    </svg>
                    <span class="nav-label">Ventas</span>
                </a>
            </li>

            <li class="nav-item dropdown-container">
                <a href="#" class="nav-link dropdown-toggler flex items-center px-4 py-3 hover:bg-gray-800 transition-colors">
                    <svg class="w-8 h-8 mr-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                        <line x1="7" y1="7" x2="7.01" y2="7"></line>
                    </svg>
                    <span class="nav-label flex-1">Marcas</span>
                </a>
                <ul class="dropdown-menu overflow-hidden transition-all duration-300 h-0" style="background-color: {{$secondaryColor}}">
                    <li class="nav-item">
                        <a href="{{route('lista-marcas')}}" class="nav-link px-12 py-2 text-sm text-gray-300 hover:text-white">Lista de Marcas</a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="{{route('lista-productos')}}" class="nav-link flex items-center px-4 py-3 hover:bg-gray-800">
                <svg class="w-8 h-8 mr-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                    <path d="M14 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                    <path d="M7 17l5 0" />
                    <path d="M3 17v-6h13v6" />
                    <path d="M5 11v-4h4" />
                    <path d="M9 11v-6h4l3 6" />
                    <path d="M22 15h-3v-10" />
                    <path d="M16 13l3 0" />
                </svg>
                    <span class="nav-label">Kardex e Inventario</span>
                </a>
            </li>
        </ul>

        <ul class="nav-list secondary-nav mt-10 pt-10 border-t border-gray-800 space-y-2">
            <li class="nav-item">
                <a href="{{ route('configuracion') }}" class="nav-link flex items-center px-4 py-3 hover:bg-gray-800 transition-colors">
                    <svg class="w-8 h-8 mr-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                    </svg>
                    <span class="nav-label">Configuración</span>
                </a>
            </li>
             <li class="nav-item">
                <a href="#" class="nav-link flex items-center px-4 py-3 hover:bg-gray-800">
                    <svg class="w-8 h-8 mr-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="12" cy="12" r="9" /> <path d="M12 17l0 .01" /> <path d="M12 13.5a1.5 1.5 0 0 1 1 -1.5a2.6 2.6 0 1 0 -3 -4" />
                    </svg>
                    <span class="nav-label">Soporte</span>
                </a>
            </li>
            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" 
                       class="nav-link flex items-center px-4 py-3 hover:bg-red-900/20 text-red-400">
                        <svg class="w-8 h-8 mr-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M10 8v-2a2 2 0 0 1 2 -2h7a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-7a2 2 0 0 1 -2 -2v-2" />
                            <path d="M15 12h-12l3 -3" /> <path d="M6 15l-3 -3" />
                        </svg>
                        <span class="nav-label">Cerrar sesión</span>
                    </a>
                </form>
            </li>
        </ul>
    </nav>
</aside>

<script src="{{asset('js/sidebar.js')}}"></script>