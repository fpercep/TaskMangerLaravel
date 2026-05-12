{{-- Sección colapsable de steps --}}
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

        {{-- Contenedor dinámico --}}
        <div class="flex-1 overflow-y-auto pr-2 transition-all duration-200 rounded-xl"
             :class="sortedSteps.length === 0 && !showNewStepInput 
                ? 'border-2 border-dashed border-gray-200 flex items-center justify-center' 
                : 'border border-gray-100/80 bg-gray-50/30 p-2'">
            
            {{-- Estado vacío --}}
            <div x-show="sortedSteps.length === 0 && !showNewStepInput" class="text-center w-full">
                <p class="text-sm text-gray-400 font-medium">Sin pasos definidos</p>
            </div>

            {{-- Lista de pasos --}}
            <div x-show="sortedSteps.length > 0 || showNewStepInput" class="w-full h-full">
                <ul class="space-y-2">
                    <template x-for="step in sortedSteps" :key="step.id">
                        <li class="group flex items-center gap-3 px-4 py-3 rounded-lg bg-white border border-gray-100 hover:border-gray-200 hover:bg-gray-50/50 transition-colors duration-200">
                            {{-- Checkbox --}}
                            <button @click="toggleStep(step)" type="button"
                                class="flex-shrink-0 w-5 h-5 rounded border-2 flex items-center justify-center transition-all duration-200 focus:outline-none"
                                :class="step.is_completed
                                    ? 'bg-green-500 border-green-500 text-white'
                                    : 'border-gray-300 hover:border-gray-400 text-transparent hover:text-gray-300'">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>

                            {{-- Nombre del paso --}}
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

                            {{-- Botón eliminar --}}
                            <button @click="deleteStep(step)" type="button"
                                class="flex-shrink-0 opacity-0 group-hover:opacity-100 text-gray-300 hover:text-red-400 transition-all duration-150 focus:outline-none p-0.5">
                                <x-lucide-x class="w-4 h-4" />
                            </button>
                        </li>
                    </template>
                </ul>

                {{-- Input nuevo paso --}}
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
