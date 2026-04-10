<header class="flex h-header-sm md:h-header-md bg-white border-b border-gray-200 shrink-0 w-full z-20 transition-all duration-300 relative" style="font-family: 'Inter', sans-serif;">
    
    <!-- LEFT SECTION -->
    <div class="flex items-center h-full min-w-0 flex-1">
        
        <!-- ESTADO EXPANDIDO: Mismo ancho que el sidebar (256px) SIN LA LÍNEA DEL BORDE (border-r) -->
        <!-- CONTROLES DE NAVEGACIÓN: Alineados con el sidebar (256px) para una estética de rejilla perfecta -->
        <div class="hidden md:flex items-center px-6 transition-all duration-300 h-full shrink-0 w-sidebar min-w-sidebar border-r border-gray-100/60">
            
            <!-- Grupo de Flechas: Estilo Navegador -->
            <div class="flex items-center gap-1">
                <x-ui.icon-button icon="chevron-left" title="Atrás" size="xl" />
                <x-ui.icon-button icon="chevron-right" title="Adelante" size="xl" />
            </div>

            <!-- Separador Minimalista -->
            <div class="h-4 w-px bg-gray-200/80 mx-4"></div>
            
            <!-- Home: Acceso Directo -->
            <x-ui.icon-button href="{{ route('dashboard') ?? '#' }}" icon="home" title="Dashboard Principal" size="lg" class="text-gray-500" />

        </div>
        
    </div>

    <!-- CENTER SECTION: Título Dashboard -->
    <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 pointer-events-none flex items-center justify-center">
        <h1 class="text-title-responsive sm:text-title-full font-bold text-gray-900 tracking-tight whitespace-nowrap" style="letter-spacing: -0.02em;">Dashboard</h1>
    </div>

    <!-- RIGHT SECTION: Usuario -->
    <div x-data="{ open: false }" class="flex items-center justify-end h-full px-4 sm:px-6 min-w-0 flex-1 relative">
        <button @click="open = !open" @keydown.escape="open = false" class="flex items-center gap-3 p-1.5 px-2.5 rounded-btn hover:bg-gray-50 transition-all focus:outline-none cursor-pointer group">
            
            @php
                $user = auth()->user();
                $userName = $user->name ?? 'Usuario';
                $userEmail = $user->email ?? 'usuario@ejemplo.com';
                $userInitials = $user->initials ?? 'US';
            @endphp
            
            <!-- Avatar Minimalista -->
            <div class="size-icon-avatar rounded-full bg-gray-100 shrink-0 flex items-center justify-center overflow-hidden transition-transform group-hover:scale-105">
                <span class="text-xs font-bold text-gray-500 uppercase">{{ $userInitials }}</span>
            </div>
            
            <!-- Nombre y Flecha -->
            <div class="hidden md:flex items-center gap-2 min-w-0">
                <span class="text-menu font-medium text-gray-600 group-hover:text-gray-900 transition-colors truncate">{{ $userName }}</span>
                <i data-lucide="chevron-down" class="size-icon-sm text-gray-400 shrink-0 transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
            </div>
            
        </button>

        <!-- Dropdown Menu -->
        <div x-show="open" 
             @click.outside="open = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             style="display: none;"
             class="absolute top-[calc(100%+0.25rem)] right-4 sm:right-6 w-dropdown bg-white rounded-dropdown shadow-[0_10px_35px_rgb(0,0,0,0.06)] border border-gray-100 p-2.5 z-50 origin-top-right transform">
            
            <!-- Botón Cerrar (X) Superior Derecha -->
            <button @click="open = false" class="absolute top-2.5 right-2.5 p-1.5 rounded-btn text-gray-400 hover:text-gray-700 hover:bg-gray-50 transition-colors focus:outline-none" title="Cerrar">
                <i data-lucide="x" class="size-icon-xs"></i>
            </button>

            <!-- Avatar y Detalles Centrados con estética más profesional -->
            <div class="flex flex-col items-center mb-4 mt-3">
                <div class="size-icon-avatar-lg rounded-full bg-[#F4F4F5] border border-gray-200 shrink-0 mb-3 flex items-center justify-center overflow-hidden">
                    <span class="text-xl font-bold text-gray-400">{{ $userInitials }}</span>
                </div>
                <h3 class="text-base font-bold text-gray-800">{{ $userName }}</h3>
                <p class="text-[13px] text-gray-400 mt-0.5">{{ $userEmail }}</p>
            </div>

            <div class="h-px w-full bg-gray-100 my-2"></div>

            <!-- Botones Limpios y Elegantes con Iconos -->
            <div class="space-y-[2px]">
                <x-ui.menu-link href="{{ route('profile.edit') }}" icon="user">
                    Mi Perfil
                </x-ui.menu-link>
                
                <x-ui.menu-link href="#" icon="settings">
                    Configuración de Cuenta
                </x-ui.menu-link>
                
                <!-- Cerrar Sesión -->
                <x-ui.menu-link method="POST" action="{{ route('logout') }}" icon="log-out" textClass="text-rose-600 hover:bg-rose-50 hover:text-rose-700">
                    Cerrar sesión
                </x-ui.menu-link>
            </div>
        </div>
    </div>
</header>