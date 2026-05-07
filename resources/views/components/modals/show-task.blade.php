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
     @keydown.escape.window="show = false">
    
    <template x-teleport="body">
        <div x-show="show"
             class="fixed inset-0 z-modal overflow-y-auto"
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
                    <x-ui.modal-header :project-name="$project->name" />

                    <!-- Cuerpo del modal -->
                    <div class="p-8 md:p-12 min-h-[70vh] flex flex-col">
                        <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-10 lg:gap-16">

                            <!-- Columna Izquierda (Principal) -->
                            <div class="flex flex-col gap-2">
                                <!-- Título -->
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

                                <!-- Metadatos inline -->
                                <div class="flex items-center gap-4 text-sm text-gray-400">
                                    <div class="flex items-center gap-1.5 hover:text-gray-600 transition-colors duration-200 cursor-default" title="Fecha de creación">
                                        <x-lucide-calendar class="w-4 h-4" />
                                        <span x-text="$formatDate(task.created_at)"></span>
                                    </div>
                                    <span class="text-gray-200">|</span>
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
                                    <div class="relative" @click.outside="assignOpen = false">
                                        <button type="button" @click="assignOpen = !assignOpen"
                                            class="flex items-center gap-1.5 hover:text-gray-600 transition-colors duration-200 cursor-pointer focus:outline-none"
                                            title="Persona asignada">
                                            <x-lucide-user class="w-4 h-4" />
                                            <span x-text="task.assigned_user ? task.assigned_user.name : 'Sin Asignar'">Sin Asignar</span>
                                            <x-lucide-chevron-down class="w-3 h-3 transition-transform duration-200" ::class="assignOpen && 'rotate-180'" />
                                        </button>

                                        <div x-show="assignOpen"
                                            style="display: none;"
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="opacity-0 scale-95"
                                            x-transition:enter-end="opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="opacity-100 scale-100"
                                            x-transition:leave-end="opacity-0 scale-95"
                                            class="absolute top-full left-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-100 py-1.5 z-30">

                                            {{-- Buscador --}}
                                            <div class="px-3 pb-2 pt-1">
                                                <div class="flex items-center gap-2 px-2.5 py-1.5 bg-gray-50 rounded-lg border border-gray-100">
                                                    <x-lucide-search class="w-3.5 h-3.5 text-gray-400 shrink-0" />
                                                    <input type="text" placeholder="Buscar miembro..."
                                                        class="w-full text-xs text-gray-700 placeholder:text-gray-400 bg-transparent border-none outline-none p-0" />
                                                </div>
                                            </div>

                                            <div class="border-t border-gray-50"></div>

                                            {{-- Opción: Sin Asignar --}}
                                            <button type="button" @click="assignOpen = false"
                                                class="w-full flex items-center gap-3 px-3 py-2 text-sm text-gray-500 hover:bg-gray-50 transition-colors">
                                                <div class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center">
                                                    <x-lucide-user-x class="w-3.5 h-3.5 text-gray-400" />
                                                </div>
                                                <span>Sin asignar</span>
                                            </button>

                                            <div class="border-t border-gray-50 my-0.5"></div>

                                            {{-- Usuarios placeholder --}}
                                            <div class="max-h-40 overflow-y-auto">
                                                <p class="px-4 py-3 text-xs text-gray-400 italic text-center">
                                                    Los miembros del proyecto aparecerán aquí.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- Columna Derecha (Sidebar) -->
                            <div class="flex flex-col gap-4">
                                <!-- Selects -->
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

                        <!-- Descripción (ancho completo, colapsable) -->
                        <div class="mt-6">
                            <div class="flex items-center gap-3">
                                <button @click="descOpen = !descOpen" type="button"
                                    class="flex items-center gap-2 group cursor-pointer focus:outline-none">
                                    <x-lucide-chevron-right class="w-4 h-4 text-gray-400 group-hover:text-gray-600 transition-all duration-200"
                                        ::class="descOpen && 'rotate-90'" />
                                    <span class="block text-xs font-semibold text-gray-500 uppercase tracking-wider group-hover:text-gray-700 transition-colors">Descripción</span>
                                </button>

                                <button @click="startEditingDesc()"
                                        x-show="!editingDesc"
                                        type="button"
                                        class="text-gray-400 hover:text-gray-600 p-1 rounded-md hover:bg-gray-100 transition-colors focus:outline-none"
                                        title="Editar descripción">
                                    <x-lucide-pencil class="w-3.5 h-3.5" />
                                </button>
                            </div>

                            <div x-show="descOpen"
                                x-transition:enter="transition-all ease-out duration-200"
                                x-transition:enter-start="opacity-0 -translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition-all ease-in duration-150"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 -translate-y-1">
                                <div x-show="!editingDesc"
                                     @dblclick="startEditingDesc()"
                                     class="mt-2 w-full cursor-text transition-colors group"
                                     title="Doble clic para editar">
                                    <!-- Estado con descripción: Hallazgo 5 (XSS) — x-text + whitespace-pre-wrap en vez de x-html -->
                                    <div x-show="task.description"
                                         class="text-base text-gray-900 bg-gray-50/50 rounded-md p-4 border border-transparent group-hover:border-gray-200 min-h-empty-md">
                                        <div x-text="task.description || ''"
                                             class="whitespace-pre-wrap"></div>
                                    </div>

                                    <!-- Estado sin descripción: Hallazgo 3 — token min-h-empty-md -->
                                    <div x-show="!task.description"
                                         class="flex items-center justify-center py-8 text-center rounded-lg border-2 border-dashed border-gray-200 group-hover:border-gray-300 group-hover:bg-gray-50/50 transition-colors min-h-empty-md">
                                        <p class="text-sm text-gray-400 font-medium">Sin descripción (doble clic para añadir)</p>
                                    </div>
                                </div>

                                <textarea
                                    x-show="editingDesc"
                                    x-ref="descInput"
                                    x-model="task.description"
                                    @blur="finishEditingDesc()"
                                    @keydown.escape="cancelEditingDesc()"
                                    placeholder="Añade una descripción a la tarea..."
                                    rows="5"
                                    class="mt-2 w-full text-base text-gray-900 placeholder:text-gray-400 bg-white border border-orange-300 focus:border-orange-300 focus:ring-2 focus:ring-orange-100 rounded-md p-4 transition-colors outline-none resize-y shadow-inner"
                                ></textarea>
                            </div>
                        </div>

                        <!-- Steps (ancho completo, colapsable): Hallazgo 3 — token min-h-steps-min -->
                        <div class="mt-6 flex-1 flex flex-col min-h-steps-min">
                            <div class="flex items-center justify-between">
                                <button @click="stepsOpen = !stepsOpen" type="button"
                                    class="flex items-center gap-2 group cursor-pointer focus:outline-none">
                                    <x-lucide-chevron-right class="w-4 h-4 text-gray-400 group-hover:text-gray-600 transition-all duration-200"
                                        ::class="stepsOpen && 'rotate-90'" />
                                    <span class="block text-xs font-semibold text-gray-500 uppercase tracking-wider group-hover:text-gray-700 transition-colors">
                                        Steps <span x-show="sortedSteps.length" x-text="completedStepsCount + '/' + sortedSteps.length" class="text-gray-400 font-normal"></span>
                                    </span>
                                </button>

                                <button @click="showNewStepInput = true; $nextTick(() => $refs.newStepInput.focus())"
                                    x-show="!showNewStepInput"
                                    type="button"
                                    class="group flex items-center gap-1.5 text-sm font-medium text-gray-600 hover:text-gray-900 bg-gray-50 hover:bg-gray-100 rounded-md px-3.5 py-1.5 transition-all duration-200 focus:outline-none">
                                    <x-lucide-plus class="w-4 h-4 text-orange-500 group-hover:text-orange-600 transition-colors" />
                                    Añadir paso
                                </button>
                            </div>

                            <div x-show="stepsOpen"
                                x-transition:enter="transition-all ease-out duration-200"
                                x-transition:enter-start="opacity-0 -translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition-all ease-in duration-150"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 -translate-y-1"
                                class="mt-3 flex-1 flex flex-col">

                                <!-- Contenedor dinámico -->
                                <div class="flex-1 overflow-y-auto pr-2 transition-all duration-200 rounded-xl"
                                     :class="sortedSteps.length === 0 && !showNewStepInput 
                                        ? 'border-2 border-dashed border-gray-200 flex items-center justify-center' 
                                        : 'border border-gray-100/80 bg-gray-50/30 p-2'">
                                    
                                    <!-- Estado vacío -->
                                    <div x-show="sortedSteps.length === 0 && !showNewStepInput" class="text-center w-full">
                                        <p class="text-sm text-gray-400 font-medium">Sin pasos definidos</p>
                                    </div>

                                    <!-- Lista de pasos (solo se muestra si hay items o input) -->
                                    <div x-show="sortedSteps.length > 0 || showNewStepInput" class="w-full h-full">
                                        <ul class="space-y-2">
                                            <template x-for="step in sortedSteps" :key="step.id">
                                                <li class="group flex items-center gap-3 px-4 py-3 rounded-lg bg-white border border-gray-100 hover:border-gray-200 hover:bg-gray-50/50 transition-colors duration-200">
                                                    <!-- Checkbox -->
                                                    <button @click="toggleStep(step)" type="button"
                                                        class="flex-shrink-0 w-5 h-5 rounded border-2 flex items-center justify-center transition-all duration-200 focus:outline-none"
                                                        :class="step.is_completed
                                                            ? 'bg-green-500 border-green-500 text-white'
                                                            : 'border-gray-300 hover:border-gray-400 text-transparent hover:text-gray-300'">
                                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </button>

                                                    <!-- Nombre del paso: Hallazgo 1 ($refs) + Hallazgo 2 (método centralizado) -->
                                                    <div class="flex-1 min-w-0" @dblclick="startEditingStep(step)">
                                                        <span x-show="editingStepId !== step.id"
                                                            class="block text-sm transition-all duration-200 cursor-text truncate"
                                                            :class="step.is_completed ? 'line-through text-gray-400' : 'text-gray-700'"
                                                            x-text="step.name"
                                                            title="Doble clic para editar"></span>

                                                        <input x-show="editingStepId === step.id"
                                                            type="text"
                                                            x-ref="stepEditInput"
                                                            x-model="editingStepName"
                                                            @blur="saveStepName(step)"
                                                            @keydown.enter="saveStepName(step)"
                                                            @keydown.escape="cancelEditingStep()"
                                                            class="w-full text-sm text-gray-700 bg-white border border-orange-300 focus:ring-2 focus:ring-orange-100 rounded px-2 py-0.5 outline-none shadow-inner" />
                                                    </div>

                                                    <!-- Botón eliminar -->
                                                    <button @click="deleteStep(step)" type="button"
                                                        class="flex-shrink-0 opacity-0 group-hover:opacity-100 text-gray-300 hover:text-red-400 transition-all duration-150 focus:outline-none p-0.5">
                                                        <x-lucide-x class="w-4 h-4" />
                                                    </button>
                                                </li>
                                            </template>
                                        </ul>

                                        <!-- Input nuevo paso -->
                                        <div x-show="showNewStepInput" x-transition class="mt-1.5">
                                            <div class="flex items-center gap-3 px-3 py-2.5 rounded-lg border border-orange-200 bg-white shadow-sm">
                                                <div class="flex-shrink-0 w-5 h-5 rounded border-2 border-gray-200"></div>
                                                <input type="text"
                                                    x-ref="newStepInput"
                                                    x-model="newStepName"
                                                    @keydown.enter="addStep()"
                                                    @keydown.escape="showNewStepInput = false; newStepName = ''"
                                                    @blur="addStep()"
                                                    placeholder="Nombre del paso..."
                                                    class="flex-1 text-sm text-gray-700 placeholder:text-gray-400 bg-transparent border-none outline-none p-0" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </template>

</div>