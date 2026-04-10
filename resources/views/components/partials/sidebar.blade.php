@php
    $currentRoute = request()->route()->getName();
@endphp

<aside 
    x-data="sidebar" 
    :class="collapsed ? 'w-[72px] min-w-[72px]' : 'w-sidebar min-w-sidebar'"
    class="h-[calc(100vh-theme(spacing.header-md))] bg-[#fdfdfd] border-r border-gray-100 flex flex-col overflow-hidden text-gray-700 font-sans transition-all duration-300 hidden md:flex shrink-0 relative group/sidebar shadow-[rgba(0,0,0,0.02)_2px_0_8px]" 
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

    <nav class="flex-1 py-5 px-3 flex flex-col overflow-y-auto overflow-x-hidden min-h-0 custom-scrollbar">

        <!-- Navegación Global -->
        <div class="space-y-1 mb-4">
            
            <!-- Search -->
            <div class="mb-3 px-1 flex justify-center">
                <template x-if="collapsed">
                    <button class="w-10 h-10 flex items-center justify-center rounded-md hover:bg-gray-100 text-gray-500 focus:outline-none transition-colors" title="Buscar...">
                        <i data-lucide="search" class="size-icon-md"></i>
                    </button>
                </template>
                <template x-if="!collapsed">
                    <div class="relative w-full">
                        <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 size-icon-md text-gray-400"></i>
                        <input 
                            type="text" 
                            placeholder="Buscar..." 
                            class="w-full bg-[#f4f5f7] border-transparent focus:border-orange-300 focus:bg-white focus:ring-2 focus:ring-orange-100 rounded-md py-1.5 pl-9 pr-3 text-sm transition-all placeholder:text-gray-400 focus:outline-none"
                        />
                    </div>
                </template>
            </div>

            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" class="group flex items-center px-2 py-2 text-[14.5px] font-medium rounded-md {{ $currentRoute === 'dashboard' ? 'bg-orange-50/70 text-orange-600' : 'text-gray-600 hover:bg-gray-100/80 hover:text-gray-900' }} transition-colors" title="Dashboard">
                <i data-lucide="layout-grid" class="size-icon-xl flex-shrink-0 {{ $currentRoute === 'dashboard' ? 'text-orange-500' : 'text-gray-400 group-hover:text-gray-600' }}" :class="collapsed ? 'mx-auto' : 'mr-3'"></i>
                <span x-show="!collapsed" x-transition.opacity.duration.300ms>Dashboard</span>
            </a>

            <!-- Task List -->
            <a href="{{ route('mi-dia') }}" class="group flex items-center px-2 py-2 text-[14.5px] font-medium rounded-md {{ $currentRoute === 'mi-dia' ? 'bg-orange-50/70 text-orange-600' : 'text-gray-600 hover:bg-gray-100/80 hover:text-gray-900' }} transition-colors" title="Lista de Tareas">
                <i data-lucide="list-todo" class="size-icon-xl flex-shrink-0 {{ $currentRoute === 'mi-dia' ? 'text-orange-500' : 'text-gray-400 group-hover:text-gray-600' }}" :class="collapsed ? 'mx-auto' : 'mr-3'"></i>
                <span x-show="!collapsed" x-transition.opacity.duration.300ms>Lista de Tareas</span>
            </a>

            <!-- Calendario -->
            <a href="#" class="group flex items-center px-2 py-2 text-[14.5px] font-medium rounded-md text-gray-600 hover:bg-gray-100/80 hover:text-gray-900 transition-colors" title="Calendario">
                <i data-lucide="calendar" class="size-icon-xl flex-shrink-0 text-gray-400 group-hover:text-gray-600" :class="collapsed ? 'mx-auto' : 'mr-3'"></i>
                <span x-show="!collapsed" x-transition.opacity.duration.300ms>Calendario</span>
            </a>
        </div>

        <div class="pt-0.5 mt-0.5 border-t border-gray-100 mx-2"></div>

        <!-- Selector de Contexto Principal (Equipo) -->
        <div class="mt-4 mb-3">
            <div class="px-1">
                <template x-if="collapsed">
                    <button class="w-10 h-10 mx-auto flex items-center justify-center rounded bg-gradient-to-br from-gray-700 to-gray-900 text-white font-semibold text-[11px] shadow-sm focus:outline-none" title="Equipo: Backend">
                        BE
                    </button>
                </template>
                <template x-if="!collapsed">
                    <button class="w-full flex items-center justify-between p-2 rounded-md hover:bg-gray-100/80 transition-colors border border-transparent focus:outline-none group">
                        <div class="flex items-center space-x-3 w-[calc(100%-20px)]">
                            <div class="w-6 h-6 rounded bg-gradient-to-br from-gray-700 to-gray-900 flex items-center justify-center text-white font-semibold text-[10px] shadow-sm flex-shrink-0">
                                BE
                            </div>
                            <span class="text-sm font-semibold text-gray-700 truncate">Equipo: Backend</span>
                        </div>
                        <i data-lucide="chevrons-up-down" class="size-icon-sm text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0"></i>
                    </button>
                </template>
            </div>
        </div>

        <div class="pt-0.5 mt-0.5 border-t border-gray-100 mx-2"></div>

        <!-- Gestión de Trabajo (Acordeones) -->
        <div class="mt-3 flex-1 flex flex-col space-y-3">
            
            <!-- Proyectos Accordion -->
            <div x-data="accordion(true)" class="flex flex-col">
                <!-- Cuando está colapsado, mostramos botón simplificado -->
                <button x-show="collapsed" class="w-10 h-10 mx-auto flex items-center justify-center rounded-md hover:bg-gray-100/80 text-gray-400 focus:outline-none transition-colors" title="Proyectos">
                    <i data-lucide="folder-kanban" class="size-icon-md"></i>
                </button>

                <!-- Cuando está expandido, mostramos acordeón -->
                <template x-if="!collapsed">
                    <div class="w-full">
                        <button @click="toggle" class="flex items-center justify-between px-2 py-1.5 w-full text-left group hover:bg-gray-50 rounded-md focus:outline-none transition-colors">
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider group-hover:text-gray-600 transition-colors">Proyectos</span>
                            <div class="flex items-center space-x-1">
                                <button @click.stop class="opacity-0 group-hover:opacity-100 p-0.5 text-gray-400 hover:text-orange-500 rounded hover:bg-orange-50 transition-all focus:outline-none">
                                    <i data-lucide="plus" class="w-3 h-3"></i>
                                </button>
                                <i data-lucide="chevron-down" class="size-icon-xs text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                            </div>
                        </button>

                        <div x-show="open" x-collapse>
                            <div class="space-y-0.5 mt-1 px-1">
                                @php
                                    $proyectos = [
                                        ['name' => 'API Gateway', 'color' => 'bg-emerald-400'],
                                        ['name' => 'Migración DB', 'color' => 'bg-indigo-400'],
                                        ['name' => 'Refactor Auth', 'color' => 'bg-orange-400'],
                                    ];
                                @endphp
                                @foreach ($proyectos as $proyecto)
                                <a href="#" class="group flex items-center justify-between px-2 py-1.5 text-[13.5px] text-gray-500 rounded-md hover:bg-gray-50 hover:text-gray-800 transition-colors">
                                    <div class="flex items-center truncate">
                                        <span class="w-[6px] h-[6px] rounded-full {{ $proyecto['color'] }} mr-2.5 flex-shrink-0 mix-blend-multiply"></span>
                                        <span class="truncate">{{ $proyecto['name'] }}</span>
                                    </div>
                                    <button class="opacity-0 group-hover:opacity-100 text-gray-400 hover:text-gray-600 p-1 focus:outline-none rounded hover:bg-gray-200 transition-colors">
                                        <i data-lucide="more-horizontal" class="size-icon-xs"></i>
                                    </button>
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Recientes Accordion -->
            <div x-data="accordion(false)" class="flex flex-col">
                <!-- Cuando está colapsado -->
                <button x-show="collapsed" class="w-10 h-10 mx-auto flex items-center justify-center rounded-md hover:bg-gray-100/80 text-gray-400 focus:outline-none transition-colors" title="Recientes">
                    <i data-lucide="clock" class="size-icon-md"></i>
                </button>

                <!-- Cuando está expandido -->
                <template x-if="!collapsed">
                    <div class="w-full">
                        <button @click="toggle" class="flex items-center justify-between px-2 py-1.5 w-full text-left group hover:bg-gray-50 rounded-md focus:outline-none transition-colors">
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider group-hover:text-gray-600 transition-colors">Recientes</span>
                            <i data-lucide="chevron-down" class="size-icon-xs text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                        </button>

                        <div x-show="open" x-collapse>
                            <div class="space-y-0.5 mt-1 px-1">
                                @php
                                    $recientes = [
                                        ['name' => 'Bug #1024', 'color' => 'bg-rose-400'],
                                        ['name' => 'Docs Swagger', 'color' => 'bg-sky-400'],
                                        ['name' => 'Reunión Semanal', 'color' => 'bg-gray-400'],
                                    ];
                                @endphp
                                @foreach ($recientes as $reciente)
                                <a href="#" class="group flex items-center justify-between px-2 py-1.5 text-[13.5px] text-gray-500 rounded-md hover:bg-gray-50 hover:text-gray-800 transition-colors">
                                    <div class="flex items-center truncate">
                                        <span class="w-[6px] h-[6px] rounded-full {{ $reciente['color'] }} mr-2.5 flex-shrink-0 mix-blend-multiply"></span>
                                        <span class="truncate">{{ $reciente['name'] }}</span>
                                    </div>
                                    <button class="opacity-0 group-hover:opacity-100 text-gray-400 hover:text-gray-600 p-1 focus:outline-none rounded hover:bg-gray-200 transition-colors">
                                        <i data-lucide="more-horizontal" class="size-icon-xs"></i>
                                    </button>
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </template>
            </div>

        </div>
    </nav>
</aside>