<x-app-layout>
    <div class="flex gap-0 -m-4 sm:-m-6 md:-m-8" x-data="layoutPanel">
      <div x-data="myDay({
            tareasHoy: {{ Js::from($tareasHoy) }},
            tareasMasTarde: {{ Js::from($tareasMasTarde) }},
            tareasAnteriores: {{ Js::from($tareasAnteriores) }},
            proyectos: {{ Js::from($proyectos) }},
            fechaHoy: '{{ now()->format('Y-m-d') }}',
            routes: {
                update: '{{ route('tasks.update', ['task' => ':id']) }}'
            }
         })"
         @task-created.window="handleRealtimeUpdate($event.detail.task)"
         @task-updated.window="handleRealtimeUpdate($event.detail.task)"
         @task-assigned.window="handleRealtimeUpdate($event.detail.task)"
         @task-steps-updated.window="handleRealtimeUpdate($event.detail.task)"
         @task-deleted.window="handleRealtimeDelete($event.detail.task_id)"
         class="flex gap-0 flex-1">

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

            <!-- Barra de progreso (visible solo si hay tareas) -->
            <div x-show="tareasHoy.length > 0" x-cloak class="mb-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-600">
                        <span x-text="progreso.completadas"></span> de <span x-text="progreso.total"></span> completadas
                    </span>
                    <span class="text-sm font-semibold text-orange-500" x-text="progreso.porcentaje + '%'"></span>
                </div>
                <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-orange-400 to-orange-500 rounded-full transition-all duration-500 ease-out"
                         :style="'width: ' + progreso.porcentaje + '%'"></div>
                </div>
            </div>

            <!-- Lista de tareas de hoy -->
            <div x-show="tareasHoy.length > 0" x-cloak class="space-y-2 flex-1 overflow-y-auto">
                {{-- Tareas pendientes primero --}}
                <template x-for="tarea in tareasHoyPendientes" :key="tarea.id">
                    <x-ui.my-day-task-card />
                </template>

                {{-- Separador si hay completadas --}}
                <template x-if="tareasHoyCompletadas.length > 0 && tareasHoyPendientes.length > 0">
                    <div class="flex items-center gap-3 py-2">
                        <div class="flex-1 h-px bg-gray-200"></div>
                        <span class="text-xs text-gray-400 font-medium">Completadas</span>
                        <div class="flex-1 h-px bg-gray-200"></div>
                    </div>
                </template>

                {{-- Tareas completadas --}}
                <template x-for="tarea in tareasHoyCompletadas" :key="tarea.id">
                    <x-ui.my-day-task-card />
                </template>
            </div>

            <!-- Estado vacío (solo si no hay tareas) -->
            <div x-show="tareasHoy.length === 0" class="flex items-start justify-center pt-8">
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

            <!-- Filtro por proyecto -->
            <div class="px-4 py-3 space-y-2 shrink-0 border-b border-gray-100">
                <div class="filter-dropdown" x-data="{ open: false }">
                    <button x-on:click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors text-sm font-medium text-gray-700 border border-gray-200">
                        <span x-text="filtroProyecto ? proyectos.find(p => p.id === filtroProyecto)?.name || 'Todos los proyectos' : 'Todos los proyectos'"></span>
                        <x-lucide-chevron-down class="size-icon-sm text-gray-400 transition-transform duration-200" x-bind:class="open ? 'rotate-180' : ''" />
                    </button>

                    <!-- Dropdown -->
                    <div x-show="open" @click.away="open = false" x-cloak
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="mt-1 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">

                        <!-- Opción: Todos -->
                        <button @click="setFiltroProyecto(null); open = false"
                                class="w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 transition-colors"
                                :class="!filtroProyecto ? 'text-orange-600 font-medium bg-orange-50/50' : 'text-gray-700'">
                            Todos los proyectos
                        </button>

                        <!-- Opciones de proyecto -->
                        <template x-for="proyecto in proyectos" :key="proyecto.id">
                            <button @click="setFiltroProyecto(proyecto.id); open = false"
                                    class="w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 transition-colors"
                                    :class="filtroProyecto === proyecto.id ? 'text-orange-600 font-medium bg-orange-50/50' : 'text-gray-700'"
                                    x-text="proyecto.name">
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Contenido con scroll (acordeones de sugerencias) -->
            <div class="flex-1 overflow-y-auto px-4 py-3">

                <!-- Sección: Más Tarde -->
                <div class="accordion-section" x-data="accordion(true)">
                    <button x-on:click="toggle()"
                        class="w-full flex items-center justify-between py-3 text-left group">
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-semibold text-gray-800 group-hover:text-orange-500 transition-colors">Más Tarde</h3>
                            <span class="text-xs text-gray-400 font-normal" x-text="'(' + sugerenciasMasTardeFiltradas.length + ')'"></span>
                        </div>
                        <x-lucide-chevron-down class="size-icon-sm text-gray-400 transition-transform duration-200" x-bind:class="open ? 'rotate-180' : ''" />
                    </button>
                    <div x-show="open" x-collapse class="space-y-2 pb-2">
                        <template x-for="tarea in sugerenciasMasTardeFiltradas" :key="tarea.id">
                            <x-ui.suggestion-card source="later" />
                        </template>

                        <div x-show="sugerenciasMasTardeFiltradas.length === 0" class="text-center py-4">
                            <p class="text-xs text-gray-400">Sin tareas en esta categoría</p>
                        </div>
                    </div>
                </div>

                <!-- Sección: Anteriores -->
                <div class="accordion-section" x-data="accordion(false)">
                    <button x-on:click="toggle()"
                        class="w-full flex items-center justify-between py-3 text-left group">
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-semibold text-gray-800 group-hover:text-orange-500 transition-colors">Anteriores</h3>
                            <span class="text-xs text-gray-400 font-normal" x-text="'(' + sugerenciasAnterioresFiltradas.length + ')'"></span>
                        </div>
                        <x-lucide-chevron-down class="size-icon-sm text-gray-400 transition-transform duration-200" x-bind:class="open ? 'rotate-180' : ''" />
                    </button>
                    <div x-show="open" x-collapse class="space-y-2 pb-2">
                        <template x-for="tarea in sugerenciasAnterioresFiltradas" :key="tarea.id">
                            <x-ui.suggestion-card source="past" dueColorClass="text-red-400" />
                        </template>

                        <div x-show="sugerenciasAnterioresFiltradas.length === 0" class="text-center py-4">
                            <p class="text-xs text-gray-400">Sin tareas anteriores</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
      </div>
    </div>
</x-app-layout>