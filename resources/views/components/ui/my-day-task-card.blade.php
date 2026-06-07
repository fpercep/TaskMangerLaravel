{{-- 
    Tarjeta de tarea para el panel principal de "Mi Día".
    Se usa dentro de un x-for en my-day.blade.php.
    El contexto Alpine padre (myDay) provee: toggleComplete(), removeFromMyDay(), priorityDotClass(), priorityLabel()
--}}
<div class="group flex items-center gap-3 py-3 transition-all duration-200"
     :class="tarea.status === 'completed' ? 'opacity-50' : ''">

    {{-- Checkbox --}}
    <button @click.stop="toggleComplete(tarea)"
            class="flex-shrink-0 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all duration-200 focus:outline-none"
            :class="tarea.status === 'completed' 
                ? 'bg-orange-400 border-orange-400 text-white' 
                : 'border-gray-300 hover:border-orange-400'">
        <svg x-show="tarea.status === 'completed'" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-50"
             x-transition:enter-end="opacity-100 scale-100"
             class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
        </svg>
    </button>

    {{-- Contenido --}}
    <div class="flex-1 min-w-0">
        <p class="text-sm font-medium truncate transition-all duration-200"
           :class="tarea.status === 'completed' ? 'text-gray-400 line-through' : 'text-gray-800'"
           x-text="tarea.name"></p>
        <div class="flex items-center gap-2 mt-0.5">
            <span class="text-xs text-orange-500 font-medium truncate" x-text="tarea.project_name"></span>
            <span class="text-gray-300">·</span>
            <span class="inline-flex items-center gap-1 text-xs text-gray-400">
                <span class="w-1.5 h-1.5 rounded-full" :class="priorityDotClass(tarea)"></span>
                <span x-text="priorityLabel(tarea)"></span>
            </span>
        </div>
    </div>

    {{-- Botón quitar de Mi Día --}}
    <button @click.stop="removeFromMyDay(tarea)"
            class="flex-shrink-0 p-1.5 rounded-lg opacity-0 group-hover:opacity-100 text-gray-400 hover:text-red-500 hover:bg-red-50 transition-all duration-200 focus:outline-none focus:opacity-100"
            title="Quitar de Mi Día">
        <x-lucide-x class="size-icon-sm" />
    </button>
</div>
