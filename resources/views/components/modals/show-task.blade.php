@props(['project'])

@php
    $statusOptions = [
        ['value' => 'pending', 'label' => 'Por hacer', 'color' => 'bg-gray-400'],
        ['value' => 'in_progress', 'label' => 'En Curso', 'color' => 'bg-blue-500'],
        ['value' => 'completed', 'label' => 'Completada', 'color' => 'bg-green-500'],
        ['value' => 'cancelled', 'label' => 'Cancelada', 'color' => 'bg-red-500'],
    ];

    $priorityOptions = [
        ['value' => 'low', 'label' => 'Baja', 'color' => 'bg-priority-low'],
        ['value' => 'medium', 'label' => 'Media', 'color' => 'bg-priority-medium'],
        ['value' => 'high', 'label' => 'Alta', 'color' => 'bg-priority-high'],
        ['value' => 'urgent', 'label' => 'Urgente', 'color' => 'bg-priority-urgent'],
    ];
@endphp

<div x-data="taskDetailModal"
     x-show="show"
     @open-task-details.window="handleOpen($event)"
     @keydown.escape.window="show = false"
     class="fixed inset-0 z-[100] overflow-y-auto"
     style="display: none;">

    <!-- Backdrop difuminado -->
    <div x-show="show" class="fixed inset-0 bg-gray-900/40 backdrop-blur-md transition-opacity" @click="show = false"
        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

    <!-- Modal Content -->
    <div x-show="show" class="relative flex min-h-screen items-center justify-center p-4 sm:p-6"
        @click.self="show = false" x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

        <div class="w-full max-w-7xl bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-200 ring-1 ring-black/5 transform transition-all">
            <!-- Header del modal -->
            <div class="px-5 py-2 border-b border-gray-100 flex items-center justify-between bg-gray-50/30">
                <div class="flex items-center gap-2 text-xs uppercase tracking-widest font-medium text-gray-400">
                    <x-lucide-folder class="w-4 h-4" />
                    <span>{{ $project->name }}</span>
                </div>

                <button @click="show = false"
                    class="text-gray-400 hover:text-gray-600 transition-colors p-1.5 rounded-lg hover:bg-gray-100 focus:outline-none">
                    <x-lucide-x class="w-5 h-5" />
                </button>
            </div>

            <!-- Cuerpo del modal -->
            <div class="p-8 md:p-12 min-h-[70vh] flex flex-col">
                <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-10 lg:gap-16 flex-1">

                    <!-- Columna Izquierda (Principal) -->
                    <div class="flex flex-col gap-2 h-full">
                        <!-- Título -->
                        <h2 class="text-2xl md:text-3xl font-semibold text-gray-900 tracking-tight" x-text="task.name"></h2>

                        <!-- Metadatos inline -->
                        <div class="flex items-center gap-4 text-sm text-gray-400">
                            <div class="flex items-center gap-1.5 hover:text-gray-600 transition-colors duration-200 cursor-default" title="Fecha de creación">
                                <x-lucide-calendar class="w-4 h-4" />
                                <span x-text="$formatDate(task.created_at)"></span>
                            </div>
                            <span class="text-gray-200">|</span>
                            <div class="flex items-center gap-1.5 hover:text-gray-600 transition-colors duration-200 cursor-default" title="Fecha de vencimiento">
                                <x-lucide-clock class="w-4 h-4" />
                                <span x-text="task.due_date ? $formatDate(task.due_date) : 'Sin definir'"></span>
                            </div>
                            <span class="text-gray-200">|</span>
                            <div class="flex items-center gap-1.5 hover:text-gray-600 transition-colors duration-200 cursor-default" title="Persona asignada">
                                <x-lucide-user class="w-4 h-4" />
                                <span>Sin Asignar</span>
                            </div>
                        </div>
                    </div>

                    <!-- Columna Derecha (Sidebar) -->
                    <div class="flex flex-col gap-4 h-full">
                        <!-- Selects -->
                        <div class="flex items-center gap-2">
                            <x-ui.color-select :options="$statusOptions" model="task.status">
                                @foreach($statusOptions as $option)
                                    <x-ui.dropdown-color-item 
                                        :color="$option['color']"
                                        x-on:click="task.status = '{{ $option['value'] }}'; open = false">
                                        {{ $option['label'] }}
                                    </x-ui.dropdown-color-item>
                                @endforeach
                            </x-ui.color-select>

                            <x-ui.color-select :options="$priorityOptions" model="task.priority">
                                @foreach($priorityOptions as $option)
                                    <x-ui.dropdown-color-item
                                        :color="$option['color']"
                                        x-on:click="task.priority = '{{ $option['value'] }}'; open = false">
                                        {{ $option['label'] }}
                                    </x-ui.dropdown-color-item>
                                @endforeach
                            </x-ui.color-select>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>