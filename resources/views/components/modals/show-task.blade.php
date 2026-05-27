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

<div x-data="taskDetailModal({
        update: '{{ route('tasks.update', ['task' => ':id']) }}',
        storeStep: '{{ route('steps.store', ['task' => ':id']) }}',
        updateStep: '{{ route('steps.update', ['step' => ':id']) }}',
        toggleStep: '{{ route('steps.toggle', ['step' => ':id']) }}',
        deleteStep: '{{ route('steps.destroy', ['step' => ':id']) }}',
    })"
     @open-task-details.window="handleOpen($event)"
     @task-updated.window="handleRealtimeUpdate($event.detail.task)"
     @task-assigned.window="handleRealtimeUpdate($event.detail.task)"
     @task-steps-updated.window="handleRealtimeUpdate($event.detail.task)"
     @task-deleted.window="handleRealtimeDelete($event.detail.task_id)"
     @keydown.escape.window="show = false">
    
    <template x-teleport="body">
        <div x-show="show"
             class="fixed inset-0 z-modal overflow-y-auto"
             style="display: none;">

            {{-- Backdrop difuminado --}}
            <div x-show="show" class="fixed inset-0 bg-gray-900/40 backdrop-blur-md transition-opacity" @click="show = false"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

            {{-- Modal Content --}}
            <div x-show="show" class="relative flex min-h-screen items-center justify-center p-4 sm:p-6"
                @click.self="show = false" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                <div class="w-full max-w-7xl bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-200 ring-1 ring-black/5 transform transition-all">
                    {{-- Header del modal --}}
                    <x-ui.modal-header :project-name="$project->name" />

                    {{-- Cuerpo del modal --}}
                    <div class="p-4 sm:p-6 md:p-12 min-h-modal-body flex flex-col">
                        <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-10 lg:gap-16">

                            {{-- Columna Izquierda (Principal) --}}
                            <div class="flex flex-col gap-2">
                                {{-- Título --}}
                                <div class="w-full">
                                    <h2 x-show="!editingTitle"
                                        @dblclick="startEditingTitle()"
                                        class="text-2xl md:text-3xl font-semibold text-gray-900 hover:text-gray-600 tracking-tight cursor-text transition-colors"
                                        x-text="task.name || 'Sin título'"
                                        title="Doble clic para editar"></h2>
                                    <input x-show="editingTitle"
                                        x-ref="titleInput"
                                        x-model="task.name"
                                        @blur="finishEditingTitle()"
                                        @keydown.enter="finishEditingTitle()"
                                        @keydown.escape="cancelEditingTitle()"
                                        class="text-2xl md:text-3xl font-semibold text-gray-900 tracking-tight bg-white border border-orange-300 focus:ring-2 focus:ring-orange-100 rounded outline-none w-full shadow-inner"
                                        type="text" />
                                </div>

                                {{-- Metadatos inline --}}
                                <div class="flex flex-wrap items-center gap-2 sm:gap-4 text-sm text-gray-400">
                                    {{-- Fecha de creación --}}
                                    <div class="flex items-center gap-1.5 hover:text-gray-600 transition-colors duration-200 cursor-default" title="Fecha de creación">
                                        <x-lucide-calendar class="w-4 h-4" />
                                        <span x-text="$formatDate(task.created_at)"></span>
                                    </div>

                                    <span class="text-gray-200">|</span>

                                    {{-- Fecha de vencimiento (editable) --}}
                                    <div class="flex items-center gap-1.5 text-gray-400 hover:text-gray-600 transition-colors duration-200 cursor-text"
                                         title="Doble clic para editar fecha"
                                         @dblclick="startEditingDate()">
                                        <x-lucide-clock class="w-4 h-4" />
                                        <span x-show="!editingDate"
                                              x-text="task.due_date ? $formatDate(task.due_date) : 'Sin definir'"></span>
                                        <input x-show="editingDate"
                                               x-ref="dateInput"
                                               type="date"
                                               x-model="task.due_date"
                                               @blur="finishEditingDate()"
                                               @keydown.enter="finishEditingDate()"
                                               @keydown.escape="cancelEditingDate()"
                                               class="text-sm text-gray-700 bg-white border border-orange-300 focus:ring-2 focus:ring-orange-100 rounded px-1 outline-none shadow-inner" />
                                    </div>

                                    <span class="text-gray-200">|</span>

                                    {{-- Dropdown de asignación --}}
                                    <x-modals.task-detail.assign-dropdown />
                                </div>

                            </div>

                            {{-- Columna Derecha (Sidebar) --}}
                            <div class="flex flex-col gap-4">
                                <div class="flex items-center gap-2">
                                    <x-ui.color-select :options="$statusOptions" model="task.status">
                                        @foreach($statusOptions as $option)
                                            <x-ui.dropdown-color-item 
                                                :color="$option['color']"
                                                x-on:click="selectField('status', '{{ $option['value'] }}'); open = false">
                                                {{ $option['label'] }}
                                            </x-ui.dropdown-color-item>
                                        @endforeach
                                    </x-ui.color-select>

                                    <x-ui.color-select :options="$priorityOptions" model="task.priority">
                                        @foreach($priorityOptions as $option)
                                            <x-ui.dropdown-color-item
                                                :color="$option['color']"
                                                x-on:click="selectField('priority', '{{ $option['value'] }}'); open = false">
                                                {{ $option['label'] }}
                                            </x-ui.dropdown-color-item>
                                        @endforeach
                                    </x-ui.color-select>
                                </div>
                            </div>
                        </div>

                        {{-- Descripción (ancho completo, colapsable) --}}
                        <x-modals.task-detail.description-section />

                        {{-- Steps (ancho completo, colapsable) --}}
                        <x-modals.task-detail.steps-section />
                    </div>

                </div>
            </div>
        </div>
    </template>

</div>