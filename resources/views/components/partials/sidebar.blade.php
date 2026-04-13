@php
    $currentRoute = request()->route()->getName();
@endphp

<aside 
    x-data="sidebar" 
    :class="collapsed ? 'w-icon-avatar-lg min-w-icon-avatar-lg' : 'w-sidebar min-w-sidebar'"
    class="h-[calc(100vh-theme(spacing.header-md))] bg-[#fdfdfd] border-r border-gray-100 flex flex-col text-gray-700 font-sans transition-all duration-300 hidden md:flex shrink-0 relative group/sidebar shadow-[rgba(0,0,0,0.02)_2px_0_8px]" 
    style="font-family: 'Inter', sans-serif;"
>
    <!-- Toggle Button -->
    <button 
        @click="toggleSidebar" 
        class="absolute top-4 -right-3.5 z-10 w-7 h-7 bg-white border border-gray-200 rounded-full flex flex-col items-center justify-center text-gray-400 hover:text-gray-600 hover:border-gray-300 shadow-sm transition-all focus:outline-none"
        title="Contraer / Mostrar menú"
    >
        <i data-lucide="chevron-left" class="size-icon-sm transition-transform duration-300" :class="collapsed ? 'rotate-180' : ''"></i>
    </button>

    <nav class="flex-1 py-5 flex flex-col overflow-y-auto overflow-x-hidden min-h-0 custom-scrollbar" :class="collapsed ? 'px-4' : 'px-3'">

        {{-- ═══════════════════════════════════════════════════
            BLOQUE 1: Navegación Global (Search + Nav Links)
        ═══════════════════════════════════════════════════ --}}
        <div class="space-y-1 mb-4">
            
            {{-- Search — Icono único con posicionamiento dinámico --}}
            <div class="mb-3 relative" :class="collapsed ? 'flex justify-center' : ''">
                {{-- Modo Colapsado: botón con icono centrado --}}
                <button 
                    x-show="collapsed" 
                    class="w-10 h-10 flex items-center justify-center rounded-md hover:bg-gray-100 text-gray-500 focus:outline-none transition-colors" 
                    title="Buscar..."
                >
                    <i data-lucide="search" class="size-icon-md"></i>
                </button>
                {{-- Modo Expandido: input con icono integrado --}}
                <div x-show="!collapsed" x-transition.opacity.duration.300ms class="relative w-full">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 size-icon-md text-gray-500"></i>
                    <input 
                        type="text" 
                        placeholder="Buscar..." 
                        class="w-full bg-[#f4f5f7] border-transparent focus:border-orange-300 focus:bg-white focus:ring-2 focus:ring-orange-100 rounded-md py-2 pl-10 pr-3 text-sm transition-all placeholder:text-gray-500 focus:outline-none"
                    />
                </div>
            </div>

            {{-- Dashboard --}}
            <a href="{{ route('dashboard') }}" 
               class="group flex items-center rounded-md {{ $currentRoute === 'dashboard' ? 'bg-orange-50/70 text-orange-600' : 'text-gray-600 hover:bg-gray-100/80 hover:text-gray-900' }} transition-colors" 
               :class="collapsed ? 'justify-center p-0' : 'px-2 py-0'"
               title="Dashboard"
            >
                <div class="w-10 h-10 flex items-center justify-center shrink-0">
                    <i data-lucide="layout-grid" class="size-icon-xl {{ $currentRoute === 'dashboard' ? 'text-orange-500' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                </div>
                <span x-show="!collapsed" x-transition.opacity.duration.300ms class="text-menu font-medium ml-1">Dashboard</span>
            </a>

            {{-- Lista de Tareas --}}
            <a href="{{ route('mi-dia') }}" 
               class="group flex items-center rounded-md {{ $currentRoute === 'mi-dia' ? 'bg-orange-50/70 text-orange-600' : 'text-gray-600 hover:bg-gray-100/80 hover:text-gray-900' }} transition-colors" 
               :class="collapsed ? 'justify-center p-0' : 'px-2 py-0'"
               title="Lista de Tareas"
            >
                <div class="w-10 h-10 flex items-center justify-center shrink-0">
                    <i data-lucide="list-todo" class="size-icon-xl {{ $currentRoute === 'mi-dia' ? 'text-orange-500' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                </div>
                <span x-show="!collapsed" x-transition.opacity.duration.300ms class="text-menu font-medium ml-1">Lista de Tareas</span>
            </a>

            {{-- Calendario --}}
            <a href="#" 
               class="group flex items-center rounded-md text-gray-600 hover:bg-gray-100/80 hover:text-gray-900 transition-colors" 
               :class="collapsed ? 'justify-center p-0' : 'px-2 py-0'"
               title="Calendario"
            >
                <div class="w-10 h-10 flex items-center justify-center shrink-0">
                    <i data-lucide="calendar" class="size-icon-xl text-gray-400 group-hover:text-gray-600"></i>
                </div>
                <span x-show="!collapsed" x-transition.opacity.duration.300ms class="text-menu font-medium ml-1">Calendario</span>
            </a>
        </div>

        {{-- Separador — global a recientes --}}
        <div class="border-t border-gray-100 mx-1"></div>

        {{-- ═══════════════════════════════════════════════════
            BLOQUE 2: Recientes
        ═══════════════════════════════════════════════════ --}}
        <div class="mt-4 mb-4 flex flex-col">
            {{-- Modo Colapsado --}}
            <div x-show="collapsed" class="flex justify-center mb-2">
                <i data-lucide="clock" class="size-icon-md text-gray-400"></i>
            </div>

            {{-- Modo Expandido --}}
            <div x-show="!collapsed">
                <div class="px-2 text-[0.75rem] font-bold text-gray-400/90 uppercase tracking-wider mb-2">
                    Recientes
                </div>
                
                {{-- No hay elementos recientes en esta vista --}}
            </div>
        </div>

        {{-- Separador — recientes a proyectos --}}
        <div class="border-t border-gray-100 mx-1"></div>

        {{-- ═══════════════════════════════════════════════════
            BLOQUE 3: Proyectos
        ═══════════════════════════════════════════════════ --}}
        <div class="mt-4 flex-1 flex flex-col">
            {{-- Modo Colapsado --}}
            <div x-show="collapsed" class="flex justify-center mb-2">
                <i data-lucide="folder-kanban" class="size-icon-md text-gray-400"></i>
            </div>

            {{-- Modo Expandido --}}
            <div x-show="!collapsed" class="flex-1 flex flex-col group/header">
                <div class="px-2 flex items-center justify-between mb-2">
                    <span class="text-[0.75rem] font-bold text-gray-400/90 uppercase tracking-wider">Proyectos</span>
                    <button @click="$dispatch('open-create-project-modal')" class="opacity-0 group-hover/header:opacity-100 p-0.5 text-gray-400 hover:text-orange-500 hover:bg-orange-50 rounded transition-all focus:outline-none" title="Nuevo proyecto">
                        <i data-lucide="plus" class="size-icon-xs"></i>
                    </button>
                </div>

                <div class="space-y-0.5">
                    @foreach ($proyectosSidebar as $proyecto)
                    <div class="group relative flex items-center justify-between px-2 py-1.5 text-[0.84375rem] text-gray-500 rounded-md hover:bg-gray-50 hover:text-gray-800 transition-colors" x-data="{ openMenu: false }">
                        <!-- Enlace principal al Proyecto -->
                        <a href="#" class="flex items-center truncate pl-2 flex-1 outline-none">
                            <span class="w-1.5 h-1.5 rounded-full {{ $proyecto->color }} mr-2.5 shrink-0 mix-blend-multiply"></span>
                            <span class="truncate">{{ $proyecto->name }}</span>
                        </a>
                        
                        <!-- Botón 3 puntos -->
                        <button @click.prevent="openMenu = !openMenu" class="opacity-0 group-hover:opacity-100 text-gray-400 hover:text-gray-600 p-1 focus:outline-none rounded hover:bg-gray-200 transition-colors cursor-pointer shrink-0" :class="openMenu ? 'opacity-100 bg-gray-200 text-gray-600' : ''">
                            <i data-lucide="more-horizontal" class="size-icon-xs"></i>
                        </button>

                        <!-- Dropdown Contextual -->
                        <div x-show="openMenu"
                             @click.outside="openMenu = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             style="display: none;"
                             class="absolute right-0 top-full mt-1 w-36 bg-white rounded-md shadow-lg border border-gray-100 py-1 z-50">
                            
                            <button @click="openMenu = false; $dispatch('open-edit-project-modal', { project: {{ json_encode($proyecto) }} })" 
                                    class="w-full text-left px-3 py-1.5 text-xs text-gray-600 hover:bg-gray-50 hover:text-gray-900 flex items-center transition-colors">
                                <i data-lucide="pencil" class="size-icon-xs mr-2 text-gray-400"></i> Editar
                            </button>
                            
                            <div class="h-px bg-gray-100 my-1"></div>
                            
                            <button @click="openMenu = false; $dispatch('open-delete-project-modal', { project: {{ json_encode($proyecto) }} })" 
                                    class="w-full text-left px-3 py-1.5 text-xs text-rose-600 hover:bg-rose-50 flex items-center transition-colors">
                                <i data-lucide="trash-2" class="size-icon-xs mr-2 text-rose-400"></i> Eliminar
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </nav>
</aside>