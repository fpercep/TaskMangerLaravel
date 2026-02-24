@extends('layouts.app')

@section('content')

    <div class="flex gap-0 -m-8" id="mi-dia-container">

        <!-- Panel Principal: Mi Día -->
        <div class="flex-1 flex flex-col min-h-0 transition-all duration-300 p-8" id="main-panel">

            <!-- Header de la página con botón toggle -->
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-1">Mi Día</h1>
                    <p class="text-sm text-gray-500">{{ $fechaHoy }}</p>
                </div>
                <!-- Botón para abrir sugerencias (visible cuando panel está cerrado) -->
                <button id="open-suggestions-btn" onclick="toggleSuggestions()"
                    class="hidden items-center gap-2 px-3 py-2 bg-orange-50 hover:bg-orange-100 text-orange-600 font-medium rounded-lg transition-all text-sm border border-orange-200">
                    <i data-lucide="panel-right-open" class="w-4 h-4"></i>
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
                        <i data-lucide="sun" class="w-12 h-12 text-orange-400"></i>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-800 mb-2">No tienes tareas para hoy</h3>
                    <p class="text-gray-500 text-sm mb-6">Comienza añadiendo tareas desde las sugerencias o crea una nueva
                        tarea.</p>

                    <button onclick="toggleSuggestions()"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-orange-400 hover:bg-orange-500 text-white font-medium rounded-lg transition-all shadow-sm hover:shadow-md text-sm">
                        <i data-lucide="lightbulb" class="w-4 h-4"></i>
                        Ver Sugerencias
                    </button>
                </div>
            </div>
        </div>

        <!-- Panel Lateral: Sugerencias -->
        <div id="suggestions-panel"
            class="w-96 h-[calc(100vh-64px)] border-l border-gray-200 bg-white flex flex-col overflow-hidden transition-all duration-300">

            <!-- Header del panel -->
            <div class="flex items-center justify-between px-4 py-4 border-b border-gray-100 shrink-0">
                <h2 class="text-lg font-semibold text-gray-900">Sugerencias</h2>
                <button onclick="toggleSuggestions()"
                    class="p-1.5 hover:bg-gray-100 rounded-lg transition-colors text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Filtros (fijos, sin scroll) -->
            <div class="px-4 py-3 space-y-2 shrink-0 border-b border-gray-100">
                <div class="filter-dropdown">
                    <button onclick="toggleDropdown(this)"
                        class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors text-sm font-medium text-gray-700 border border-gray-200">
                        <span>Equipo 1</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 transition-transform duration-200"></i>
                    </button>
                </div>
                <div class="filter-dropdown">
                    <button onclick="toggleDropdown(this)"
                        class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors text-sm font-medium text-gray-700 border border-gray-200">
                        <span>Todos los proyectos</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 transition-transform duration-200"></i>
                    </button>
                </div>
            </div>

            <!-- Contenido con scroll (solo acordeones) -->
            <div class="flex-1 overflow-y-auto px-4 py-3">

                <!-- Sección: Más Tarde -->
                <div class="accordion-section">
                    <button onclick="toggleAccordion(this)"
                        class="w-full flex items-center justify-between py-3 text-left group">
                        <h3 class="text-sm font-semibold text-gray-800 group-hover:text-orange-500 transition-colors">Más
                            Tarde</h3>
                        <i data-lucide="chevron-down"
                            class="w-4 h-4 text-gray-400 transition-transform duration-200 accordion-icon"></i>
                    </button>
                    <div class="accordion-content space-y-2 pb-2">
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
                                <button onclick="addToMyDay(event)"
                                    class="opacity-0 group-hover:opacity-100 p-1.5 text-orange-400 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition-all">
                                    <i data-lucide="plus" class="w-4 h-4"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Sección: Anteriores -->
                <div class="accordion-section">
                    <button onclick="toggleAccordion(this)"
                        class="w-full flex items-center justify-between py-3 text-left group">
                        <h3 class="text-sm font-semibold text-gray-800 group-hover:text-orange-500 transition-colors">
                            Anteriores</h3>
                        <i data-lucide="chevron-down"
                            class="w-4 h-4 text-gray-400 transition-transform duration-200 accordion-icon"></i>
                    </button>
                    <div class="accordion-content space-y-2 pb-2">
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
                                <button onclick="addToMyDay(event)"
                                    class="opacity-0 group-hover:opacity-100 p-1.5 text-orange-400 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition-all">
                                    <i data-lucide="plus" class="w-4 h-4"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .accordion-content {
            max-height: 1000px;
            overflow: hidden;
            transition: max-height 0.3s ease-out, opacity 0.2s ease-out;
            opacity: 1;
        }

        .accordion-content.collapsed {
            max-height: 0;
            opacity: 0;
        }

        .accordion-icon.rotated {
            transform: rotate(180deg);
        }

        #suggestions-panel.hidden-panel {
            width: 0;
            padding: 0;
            border: none;
            overflow: hidden;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function toggleSuggestions() {
            const panel = document.getElementById('suggestions-panel');
            const openBtn = document.getElementById('open-suggestions-btn');

            panel.classList.toggle('hidden-panel');

            if (panel.classList.contains('hidden-panel')) {
                openBtn.classList.remove('hidden');
                openBtn.classList.add('flex');
            } else {
                openBtn.classList.add('hidden');
                openBtn.classList.remove('flex');
            }

            setTimeout(() => lucide.createIcons(), 300);
        }

        function toggleAccordion(button) {
            const section = button.closest('.accordion-section');
            const content = section.querySelector('.accordion-content');
            const icon = button.querySelector('.accordion-icon');

            content.classList.toggle('collapsed');
            icon.classList.toggle('rotated');
        }

        function toggleDropdown(button) {
            const icon = button.querySelector('i');
            icon.classList.toggle('rotate-180');
        }

        document.addEventListener('DOMContentLoaded', function () {
            lucide.createIcons();
        });
    </script>
@endpush