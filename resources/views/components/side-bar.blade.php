<aside class="sidebar text-white min-h-screen transition-all duration-300" style="background-color: {{$primaryColor}}">
    <nav class="sidebar-nav py-6 mt-5">
        <ul class="nav-list primary-nav space-y-2">
            
            <li class="nav-item dropdown-container">
                <a href="#" class="nav-link dropdown-toggler flex items-center px-4 py-3 hover:bg-gray-800 transition-colors">
                    <svg class="w-8 h-8 mr-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 4a1 1 0 0 1 1 -1h16a1 1 0 0 1 1 1v12a1 1 0 0 1 -1 1h-16a1 1 0 0 1 -1 -1v-12z" />
                        <path d="M3 13h18" /> <path d="M8 21h8" /> <path d="M10 17l-.5 4" /> <path d="M14 17l.5 4" />
                    </svg>
                    <span class="nav-label flex-1">Escritorio</span>
                </a>
                <ul class="dropdown-menu overflow-hidden transition-all duration-300 h-0" style="background-color: {{$secondaryColor}}">
                    <li class="nav-item">
                        <a href="#" class="nav-link px-12 py-2 text-sm text-gray-300 hover:text-white">Ver Resumen</a>
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
                <a href="#" class="nav-link flex items-center px-4 py-3 hover:bg-gray-800">
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
                <svg
                class="w-8 h-8 mr-3"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
                stroke-linecap="round"
                stroke-linejoin="round"
                >
                <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                <path d="M9 17h6" />
                <path d="M9 13h6" />
                </svg>
                    <span class="nav-label flex-1">Creditos</span>
                </a>
                <ul class="dropdown-menu overflow-hidden transition-all duration-300 h-0" style="background-color: {{$secondaryColor}}">
                    <li class="nav-item">
                        <a href="#" class="nav-link px-12 py-2 text-sm text-gray-300 hover:text-white">Creditos</a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="#" class="nav-link flex items-center px-4 py-3 hover:bg-gray-800">

                <svg
                class="w-8 h-8 mr-3"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1"
                stroke-linecap="round"
                stroke-linejoin="round"
                >
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
            