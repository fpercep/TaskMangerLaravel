{{-- Sección colapsable de descripción --}}
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
            {{-- Con descripción --}}
            <div x-show="task.description"
                 class="text-base text-gray-900 bg-gray-50/50 rounded-md p-4 border border-transparent group-hover:border-gray-200 min-h-empty-md">
                <div x-text="task.description || ''"
                     class="whitespace-pre-wrap"></div>
            </div>

            {{-- Sin descripción --}}
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
