{{-- Dropdown de asignación de usuario --}}
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
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-[-10px]"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-[-10px]"
        class="absolute top-full left-0 mt-3 w-64 bg-white rounded-2xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.15)] border border-gray-100 z-50 overflow-hidden">

        {{-- Contenido placeholder — Funcionalidad en desarrollo --}}
        <div class="p-5 flex flex-col items-center justify-center gap-3 text-center">
            <div class="w-10 h-10 rounded-full bg-orange-50 flex items-center justify-center">
                <x-lucide-users class="w-5 h-5 text-orange-400" />
            </div>
            <div>
                <p class="text-sm font-medium text-gray-700">Asignación de usuarios</p>
                <p class="text-xs text-gray-400 mt-1">Esta función estará disponible próximamente.</p>
            </div>
        </div>
    </div>
</div>
