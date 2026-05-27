<x-app-layout>
    <div class="flex gap-0 -m-4 sm:-m-6 md:-m-8" x-data="layoutPanel">

        <!-- Panel Principal: Mi Día -->
        <div class="flex-1 flex flex-col min-h-0 transition-all duration-300 p-4 sm:p-6 md:p-8">

            <!-- Header de la página con botón toggle -->
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">Mi Día</h1>
                    <p class="text-sm text-gray-500">{{ $fechaHoy }}</p>
                </div>
                <!-- Botón para abrir sugerencias (visible cuando panel está cerrado) -->
                <button x-show="!showSuggestions" x-on:click="toggleSuggestions()" x-cloak
                    class="flex items-center gap-2 px-3 py-2 bg-orange-50 hover:bg-orange-100 text-orange-600 font-medium rounded-lg transition-all text-sm border border-orange-200">
                    <x-lucide-panel-right-open class="size-icon-sm" />
                    <span class="hidden sm:inline">Sugerencias</span>
                </button>
            </div>

            <!-- Estado vacío - más arriba en la página -->
            <div class="flex items-start justify-center pt-8">
                <div
                    class="text-center max-w-md mx-auto p-6 sm:p-8 bg-gradient-to-br from-gray-50 to-white rounded-2xl border border-gray-100 shadow-sm">

                    <!-- Ilustración -->
                    <div
                        class="w-20 h-20 sm:w-24 sm:h-24 mx-auto mb-4 sm:mb-6 bg-gradient-to-br from-orange-100 to-orange-50 rounded-2xl flex items-center justify-center">
                        <x-lucide-sun class="size-icon-avatar text-orange-400" />
                    </div>

                    <h3 class="text-lg font-semibold text-gray-800 mb-2">No tienes tareas para hoy</h3>
                    <p class="text-gray-500 text-sm mb-6">Comienza añadiendo tareas desde las sugerencias o crea una nueva
                        tarea.</p>

                    <button x-on:click="toggleSuggestions()"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-orange-400 hover:bg-orange-500 text-white font-medium rounded-lg transition-all shadow-sm hover:shadow-md text-sm">
                        <x-lucide-lightbulb class="size-icon-sm" />
                        Ver Sugerencias
                    </button>
                </div>
            </div>
        </div>

        <!-- Panel Lateral: Sugerencias (Desktop: sidebar, Mobile: overlay) -->
        {{-- Mobile backdrop --}}
        <div x-show="showSuggestions" x-cloak
             class="md:hidden fixed inset-0 bg-black/40 z-30"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="toggleSuggestions()"></div>

        <div x-show="showSuggestions" x-cloak
            class="fixed md:relative inset-y-0 right-0 z-31 w-full sm:w-80 md:w-96 md:h-[calc(100vh-theme(spacing.header-md))] border-l border-gray-200 bg-white flex flex-col overflow-hidden"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="translate-x-full md:translate-x-0"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full md:translate-x-0">

            <!-- Header del panel -->
            <div class="flex items-center justify-between px-4 py-4 border-b border-gray-100 shrink-0">
                <h2 class="text-lg font-semibold text-gray-900">Sugerencias</h2>
                <button x-on:click="toggleSuggestions()"
                    class="p-1.5 hover:bg-gray-100 rounded-lg transition-colors text-gray-400 hover:text-gray-600">
                    <x-lucide-x class="size-icon-xl" />
                </button>
            </div>

            <!-- Filtros (fijos, sin scroll) -->
            <div class="px-4 py-3 space-y-2 shrink-0 border-b border-gray-100">

                <div class="filter-dropdown" x-data="{ open: false }">
                    <button x-on:click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors text-sm font-medium text-gray-700 border border-gray-200">
                        <span>Todos los proyectos</span>
                        <x-lucide-chevron-down class="size-icon-sm text-gray-400 transition-transform duration-200" x-bind:class="open ? 'rotate-180' : ''" />
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
                        <x-lucide-chevron-down class="size-icon-sm text-gray-400 transition-transform duration-200" x-bind:class="open ? 'rotate-180' : ''" />
                    </button>
                    <div x-show="open" x-collapse class="space-y-2 pb-2">
                        @foreach ($tareasMasTarde as $tarea)
                            <x-ui.suggestion-card :tarea="$tarea" />
                        @endforeach
                    </div>
                </div>

                <!-- Sección: Anteriores -->
                <div class="accordion-section" x-data="accordion(false)">
                    <button x-on:click="toggle()"
                        class="w-full flex items-center justify-between py-3 text-left group">
                        <h3 class="text-sm font-semibold text-gray-800 group-hover:text-orange-500 transition-colors">Anteriores</h3>
                        <x-lucide-chevron-down class="size-icon-sm text-gray-400 transition-transform duration-200" x-bind:class="open ? 'rotate-180' : ''" />
                    </button>
                    <div x-show="open" x-collapse class="space-y-2 pb-2">
                        @foreach ($tareasAnteriores as $tarea)
                            <x-ui.suggestion-card :tarea="$tarea" />
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>