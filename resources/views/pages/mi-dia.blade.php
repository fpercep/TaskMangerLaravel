<x-app-layout>
    <div class="flex gap-0 -m-8" x-data="layoutPanel">

        <!-- Panel Principal: Mi Día -->
        <div class="flex-1 flex flex-col min-h-0 transition-all duration-300 p-8">

            <!-- Header de la página con botón toggle -->
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-1">Mi Día</h1>
                    <p class="text-sm text-gray-500">{{ $fechaHoy }}</p>
                </div>
                <!-- Botón para abrir sugerencias (visible cuando panel está cerrado) -->
                <button x-show="!showSuggestions" x-on:click="toggleSuggestions()" x-cloak
                    class="flex items-center gap-2 px-3 py-2 bg-orange-50 hover:bg-orange-100 text-orange-600 font-medium rounded-lg transition-all text-sm border border-orange-200">
                    <i data-lucide="panel-right-open" class="size-icon-sm"></i>
                    <span>Sugerencias</span>
                </button>
            </div>

            <!-- Estado vacío - más arriba en la página -->
            <div class="flex items-start justify-center pt-8">
                <div
                    class="text-center max-w-md mx-auto p-8 bg-gradient-to-br from-gray-50 to-white rounded-2xl border border-gray-100 shadow-sm">

                    <!-- Ilustración -->
                    <div
                        class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-orange-100 to-orange-50 rounded-2xl flex items-center justify-center">
                        <i data-lucide="sun" class="size-icon-avatar text-orange-400"></i>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-800 mb-2">No tienes tareas para hoy</h3>
                    <p class="text-gray-500 text-sm mb-6">Comienza añadiendo tareas desde las sugerencias o crea una nueva
                        tarea.</p>

                    <button x-on:click="toggleSuggestions()"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-orange-400 hover:bg-orange-500 text-white font-medium rounded-lg transition-all shadow-sm hover:shadow-md text-sm">
                        <i data-lucide="lightbulb" class="size-icon-sm"></i>
                        Ver Sugerencias
                    </button>
                </div>
            </div>
        </div>

        <!-- Panel Lateral: Sugerencias -->
        <div x-show="showSuggestions" x-collapse:horizontal x-cloak
            class="w-96 h-[calc(100vh-theme(spacing.header-md))] border-l border-gray-200 bg-white flex flex-col overflow-hidden">

            <!-- Header del panel -->
            <div class="flex items-center justify-between px-4 py-4 border-b border-gray-100 shrink-0">
                <h2 class="text-lg font-semibold text-gray-900">Sugerencias</h2>
                <button x-on:click="toggleSuggestions()"
                    class="p-1.5 hover:bg-gray-100 rounded-lg transition-colors text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="size-icon-xl"></i>
                </button>
            </div>

            <!-- Filtros (fijos, sin scroll) -->
            <div class="px-4 py-3 space-y-2 shrink-0 border-b border-gray-100">
                <div class="filter-dropdown" x-data="{ open: false }">
                    <button x-on:click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors text-sm font-medium text-gray-700 border border-gray-200">
                        <span>Equipo 1</span>
                        <i data-lucide="chevron-down" class="size-icon-sm text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                </div>
                <div class="filter-dropdown" x-data="{ open: false }">
                    <button x-on:click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors text-sm font-medium text-gray-700 border border-gray-200">
                        <span>Todos los proyectos</span>
                        <i data-lucide="chevron-down" class="size-icon-sm text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                </div>
            </div>

            <!-- Contenido con scroll (solo acordeones) -->
            <div class="flex-1 overflow-y-auto px-4 py-3">

                <!-- Sección: Más Tarde -->
                <div class="accordion-section" x-data="accordion(true)">
                    <button x-on:click="toggle()"
                        class="w-full flex items-center justify-between py-3 text-left group">
                        <h3 class="text-sm font-semibold text-gray-800 group-hover:text-orange-500 transition-colors">Más Tarde</h3>
                        <i data-lucide="chevron-down"
                            class="size-icon-sm text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-collapse class="space-y-2 pb-2">
                        @foreach ($tareasMasTarde as $tarea)
                            <div
                                class="group flex items-center justify-between p-3 bg-gray-50 hover:bg-white rounded-lg border border-transparent hover:border-gray-200 hover:shadow-sm transition-all cursor-pointer">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate">
                                        {{ $tarea['titulo'] }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        <span class="text-orange-500">
                                            {{ $tarea['equipo'] }}
                                        </span> /
                                        {{ $tarea['proyecto'] }} ·
                                        {{ $tarea['fecha'] }}
                                    </p>
                                </div>
                                <button
                                    class="opacity-0 group-hover:opacity-100 p-1.5 text-orange-400 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition-all">
                                    <i data-lucide="plus" class="size-icon-sm"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Sección: Anteriores -->
                <div class="accordion-section" x-data="accordion(false)">
                    <button x-on:click="toggle()"
                        class="w-full flex items-center justify-between py-3 text-left group">
                        <h3 class="text-sm font-semibold text-gray-800 group-hover:text-orange-500 transition-colors">Anteriores</h3>
                        <i data-lucide="chevron-down"
                            class="size-icon-sm text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-collapse class="space-y-2 pb-2">
                        @foreach ($tareasAnteriores as $tarea)
                            <div
                                class="group flex items-center justify-between p-3 bg-gray-50 hover:bg-white rounded-lg border border-transparent hover:border-gray-200 hover:shadow-sm transition-all cursor-pointer">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate">
                                        {{ $tarea['titulo'] }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        <span class="text-orange-500">
                                            {{ $tarea['equipo'] }}
                                        </span> /
                                        {{ $tarea['proyecto'] }} ·
                                        {{ $tarea['fecha'] }}
                                    </p>
                                </div>
                                <button
                                    class="opacity-0 group-hover:opacity-100 p-1.5 text-orange-400 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition-all">
                                    <i data-lucide="plus" class="size-icon-sm"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>