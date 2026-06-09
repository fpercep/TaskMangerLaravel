@php
    $currentRoute = request()->route()->getName();
    $activeProjectId = request()->route('project')?->id;
@endphp

{{-- ═══════════════════════════════════════════════════
    MOBILE SIDEBAR (overlay, visible only on < md)
═══════════════════════════════════════════════════ --}}
<div x-data="sidebar"
     @toggle-mobile-sidebar.window="toggleMobile()"
     @project-removed.window="handleProjectRemoved($event.detail)"
     @project-added.window="handleProjectAdded($event.detail)"
     @project-updated.window="handleProjectUpdated($event.detail)">

    {{-- Mobile Backdrop --}}
    <div class="md:hidden sidebar-mobile-backdrop"
         :class="mobileOpen && 'is-open'"
         @click="closeMobile()"></div>

    {{-- Mobile Panel --}}
    <div class="md:hidden sidebar-mobile-panel flex flex-col"
         :class="mobileOpen && 'is-open'"
         style="font-family: 'Inter', sans-serif;">

        {{-- Mobile header with close button --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <span class="text-sm font-bold text-gray-700 tracking-tight">Menú</span>
            <button @click="closeMobile()" class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors focus:outline-none">
                <x-lucide-x class="size-icon-lg" />
            </button>
        </div>

        <nav class="flex-1 py-4 px-3 flex flex-col overflow-y-auto scrollbar-mobile-hide">
            {{-- Navegación Global --}}
            <div class="space-y-1 mb-4">
                <a href="{{ route('dashboard') }}" @click="closeMobile()"
                   class="group flex items-center px-2 py-0 rounded-md {{ $currentRoute === 'dashboard' ? 'bg-orange-50/70 text-orange-600' : 'text-gray-600 hover:bg-gray-100/80 hover:text-gray-900' }} transition-colors">
                    <div class="w-10 h-10 flex items-center justify-center shrink-0">
                        <x-lucide-layout-grid class="size-icon-xl {{ $currentRoute === 'dashboard' ? 'text-orange-500' : 'text-gray-400 group-hover:text-gray-600' }}" />
                    </div>
                    <span class="text-menu font-medium ml-1">Dashboard</span>
                </a>

                <a href="{{ route('mi-dia') }}" @click="closeMobile()"
                   class="group flex items-center px-2 py-0 rounded-md {{ $currentRoute === 'mi-dia' ? 'bg-orange-50/70 text-orange-600' : 'text-gray-600 hover:bg-gray-100/80 hover:text-gray-900' }} transition-colors">
                    <div class="w-10 h-10 flex items-center justify-center shrink-0">
                        <x-lucide-list-todo class="size-icon-xl {{ $currentRoute === 'mi-dia' ? 'text-orange-500' : 'text-gray-400 group-hover:text-gray-600' }}" />
                    </div>
                    <span class="text-menu font-medium ml-1">Mi día</span>
                </a>

                <a href="#" class="group flex items-center px-2 py-0 rounded-md text-gray-600 hover:bg-gray-100/80 hover:text-gray-900 transition-colors">
                    <div class="w-10 h-10 flex items-center justify-center shrink-0">
                        <x-lucide-calendar class="size-icon-xl text-gray-400 group-hover:text-gray-600" />
                    </div>
                    <span class="text-menu font-medium ml-1">Calendario</span>
                </a>
            </div>

            {{-- Separador --}}
            <div class="border-t border-gray-100 mx-1"></div>

            {{-- Recientes --}}
            <div class="mt-4 mb-4 flex flex-col">
                <div class="px-2 text-[0.75rem] font-bold text-gray-400/90 uppercase tracking-wider mb-2">
                    Recientes
                </div>
            </div>

            {{-- Separador --}}
            <div class="border-t border-gray-100 mx-1"></div>

            {{-- Proyectos --}}
            <div class="mt-4 flex-1 flex flex-col">
                <div class="px-2 flex items-center justify-between mb-2">
                    <span class="text-[0.75rem] font-bold text-gray-400/90 uppercase tracking-wider">Proyectos</span>
                    <button @click="$dispatch('open-modal', { name: 'create-project' }); closeMobile()" class="p-0.5 text-gray-400 hover:text-orange-500 hover:bg-orange-50 rounded transition-all focus:outline-none">
                        <x-lucide-plus class="size-icon-xs" />
                    </button>
                </div>

                <div class="space-y-0.5">
                    @foreach ($proyectosSidebar as $proyecto)
                    @php
                        $isProjectActive = $activeProjectId == $proyecto->id;
                    @endphp
                    <a href="{{ route('projects.show', $proyecto->id) }}" @click="closeMobile()"
                       class="flex items-center truncate pl-4 py-1.5 text-sidebar-item font-medium {{ $isProjectActive ? 'bg-orange-50/70 text-orange-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800' }} rounded-md transition-colors">
                        <span class="w-2 h-2 rounded-full {{ $proyecto->color ?? 'bg-orange-500' }} mr-2.5 shrink-0 {{ $isProjectActive ? '' : 'mix-blend-multiply' }}"></span>
                        <span class="truncate {{ $isProjectActive ? 'font-semibold' : '' }}">{{ $proyecto->name }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
        </nav>

        @if(auth()->check() && auth()->user()->is_super_admin)
        <div class="border-t border-gray-100 p-3 bg-[#fdfdfd]">
            <a href="{{ route('admin.index') }}" @click="closeMobile()"
               class="group flex items-center justify-center rounded-md text-gray-600 hover:bg-gray-100/80 hover:text-gray-900 transition-colors h-10 w-full">
                <x-lucide-settings class="size-icon-xl text-gray-400 group-hover:text-gray-600 mr-2" />
                <span class="text-menu font-medium whitespace-nowrap">Panel de Administración</span>
            </a>
        </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════════
        DESKTOP SIDEBAR (original behavior, visible on md+)
    ═══════════════════════════════════════════════════ --}}
    <aside
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
            <x-lucide-chevron-left class="size-icon-sm transition-transform duration-300" x-bind:class="collapsed ? 'rotate-180' : ''" />
        </button>

        <nav class="flex-1 py-5 flex flex-col overflow-y-auto overflow-x-hidden min-h-0 custom-scrollbar" :class="collapsed ? 'px-4' : 'px-3'">

            {{-- ═══════════════════════════════════════════════════
                BLOQUE 1: Navegación Global (Search + Nav Links)
            ═══════════════════════════════════════════════════ --}}
            <div class="space-y-1 mb-4">
                

                {{-- Dashboard --}}
                <a href="{{ route('dashboard') }}"
                   class="group flex items-center rounded-md {{ $currentRoute === 'dashboard' ? 'bg-orange-50/70 text-orange-600' : 'text-gray-600 hover:bg-gray-100/80 hover:text-gray-900' }} transition-colors"
                   :class="collapsed ? 'justify-center p-0' : 'px-2 py-0'"
                   title="Dashboard"
                >
                    <div class="w-10 h-10 flex items-center justify-center shrink-0">
                        <x-lucide-layout-grid class="size-icon-xl {{ $currentRoute === 'dashboard' ? 'text-orange-500' : 'text-gray-400 group-hover:text-gray-600' }}" />
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
                        <x-lucide-list-todo class="size-icon-xl {{ $currentRoute === 'mi-dia' ? 'text-orange-500' : 'text-gray-400 group-hover:text-gray-600' }}" />
                    </div>
                    <span x-show="!collapsed" x-transition.opacity.duration.300ms class="text-menu font-medium ml-1">Mi día</span>
                </a>

                {{-- Calendario --}}
                <a href="#"
                   class="group flex items-center rounded-md text-gray-600 hover:bg-gray-100/80 hover:text-gray-900 transition-colors"
                   :class="collapsed ? 'justify-center p-0' : 'px-2 py-0'"
                   title="Calendario"
                >
                    <div class="w-10 h-10 flex items-center justify-center shrink-0">
                        <x-lucide-calendar class="size-icon-xl text-gray-400 group-hover:text-gray-600" />
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
                    <x-lucide-clock class="size-icon-md text-gray-400" />
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
                    <x-lucide-folder-kanban class="size-icon-md text-gray-400" />
                </div>

                {{-- Modo Expandido --}}
                <div x-show="!collapsed" class="flex-1 flex flex-col group/header">
                    <div class="px-2 flex items-center justify-between mb-2">
                        <span class="text-[0.75rem] font-bold text-gray-400/90 uppercase tracking-wider">Proyectos</span>
                        <button @click="$dispatch('open-modal', { name: 'create-project' })" class="opacity-0 group-hover/header:opacity-100 p-0.5 text-gray-400 hover:text-orange-500 hover:bg-orange-50 rounded transition-all focus:outline-none" title="Nuevo proyecto">
                            <x-lucide-plus class="size-icon-xs" />
                        </button>
                    </div>

                    <div class="space-y-0.5" x-ref="projectList">
                        @foreach ($proyectosSidebar as $proyecto)
                        @php
                            $isProjectActive = $activeProjectId == $proyecto->id;
                        @endphp
                        <div class="group relative flex items-center justify-between px-2 py-1.5 text-sidebar-item font-medium {{ $isProjectActive ? 'bg-orange-50/70 text-orange-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800' }} rounded-md transition-colors"
                             x-data="{ openMenu: false, role: '{{ $proyecto->role ?? 'editor' }}' }"
                             @project-member-updated.window="if ($event.detail.project_id === {{ $proyecto->id }} && $event.detail.user_id === window.AppUserId) { role = $event.detail.role; }"
                        >
                            <!-- Enlace principal al Proyecto -->
                            <a href="{{ route('projects.show', $proyecto->id) }}" class="flex items-center truncate pl-2 flex-1 outline-none">
                                <span class="w-2 h-2 rounded-full {{ $proyecto->color ?? 'bg-orange-500' }} mr-2.5 shrink-0 {{ $isProjectActive ? '' : 'mix-blend-multiply' }}"></span>
                                <span class="truncate {{ $isProjectActive ? 'font-semibold' : '' }}">{{ $proyecto->name }}</span>
                            </a>
                            
                            <!-- Botón 3 puntos -->
                            <button @click.prevent="openMenu = !openMenu" class="opacity-0 group-hover:opacity-100 text-gray-400 hover:text-gray-600 p-1 focus:outline-none rounded hover:bg-gray-200 transition-colors cursor-pointer shrink-0 mr-1" :class="openMenu ? 'opacity-100 bg-gray-200 text-gray-600' : ''">
                                <x-lucide-more-horizontal class="size-icon-xs" />
                            </button>

                            <x-ui.context-menu>
                                <x-ui.dropdown-item
                                    icon="pencil"
                                    x-show="role === 'admin' || role === 'manager'"
                                    @click.stop='openMenu = false; editProject({{ $proyecto->id }}, {{ Js::from($proyecto->name) }}, {{ Js::from($proyecto->description) }})'
                                >
                                    Editar
                                </x-ui.dropdown-item>

                                <x-ui.dropdown-item
                                    icon="user-round-cog"
                                    x-show="role === 'admin' || role === 'manager'"
                                    @click.stop="openMenu = false; $dispatch('open-modal', { name: 'manage-users', payload: { project: {{ Js::from($proyecto) }} } })"
                                >
                                    Usuarios
                                </x-ui.dropdown-item>

                                <div class="h-px bg-gray-100 my-1" x-show="role === 'admin'"></div>

                                <x-ui.dropdown-item
                                    icon="trash-2"
                                    :destructive="true"
                                    x-show="role === 'admin'"
                                    @click.stop='openMenu = false; deleteProject({{ $proyecto->id }}, {{ Js::from($proyecto->name) }})'
                                >
                                    Eliminar
                                </x-ui.dropdown-item>

                                <x-ui.dropdown-item
                                    icon="log-out"
                                    :destructive="true"
                                    @click.stop='openMenu = false; leaveProject({{ $proyecto->id }}, {{ Js::from($proyecto->name) }})'
                                >
                                    Abandonar
                                </x-ui.dropdown-item>
                            </x-ui.context-menu>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </nav>

        @if(auth()->check() && auth()->user()->is_super_admin)
        <div class="border-t border-gray-100 p-3 bg-[#fdfdfd]">
            <a href="{{ route('admin.index') }}"
               class="group flex items-center justify-center rounded-md text-gray-600 hover:bg-gray-100/80 hover:text-gray-900 transition-colors h-10 w-full"
               title="Panel de Administración"
            >
                <x-lucide-settings class="size-icon-xl text-gray-400 group-hover:text-gray-600" x-bind:class="collapsed ? '' : 'mr-2'" />
                <span x-show="!collapsed" x-transition.opacity.duration.300ms class="text-menu font-medium whitespace-nowrap">Panel de Administración</span>
            </a>
        </div>
        @endif
    </aside>
</div>