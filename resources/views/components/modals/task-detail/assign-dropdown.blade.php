{{-- Dropdown de asignación de usuario --}}
{{-- Dropdown de asignación de usuario --}}
<div class="relative" @click.outside="assignOpen = false">
    <button type="button" @click="assignOpen = !assignOpen"
        class="flex items-center gap-1.5 hover:text-gray-600 transition-colors duration-200 cursor-pointer focus:outline-none group"
        title="Persona asignada">
        <template x-if="task.assigned_user">
            <div class="flex items-center gap-2 transition-colors">
                <div class="w-6 h-6 rounded-full bg-gray-50 flex items-center justify-center text-[10px] font-medium text-gray-400 border border-gray-100 group-hover:bg-gray-100 group-hover:text-gray-500 transition-all" 
                     x-text="task.assigned_user.initials"></div>
                <span class="text-gray-400 group-hover:text-gray-600 transition-colors" x-text="task.assigned_user.name"></span>
            </div>
        </template>
        <template x-if="!task.assigned_user">
            <div class="flex items-center gap-2 transition-colors">
                <div class="w-6 h-6 flex items-center justify-center">
                    <x-lucide-user class="w-4.5 h-4.5 text-gray-400 group-hover:text-gray-600 transition-colors" />
                </div>
                <span class="text-gray-400 group-hover:text-gray-600 transition-colors">Sin Asignar</span>
            </div>
        </template>
        <x-lucide-chevron-down class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200" ::class="assignOpen && 'rotate-180'" />
    </button>

    <div x-show="assignOpen"
        style="display: none;"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-[-10px]"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-[-10px]"
        class="absolute top-full left-0 mt-3 w-72 bg-white rounded-2xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.15)] border border-gray-100 z-50 overflow-hidden">
        
        {{-- Buscador --}}
        <div class="p-3 border-b border-gray-50 bg-gray-50/50">
            <div class="relative">
                <x-lucide-search class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" />
                <input type="text" 
                    x-model="assignQuery"
                    placeholder="Buscar miembro..." 
                    class="w-full bg-white border border-gray-200 rounded-lg pl-8 pr-3 py-1.5 text-xs placeholder:text-gray-400 focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition-all outline-none">
            </div>
        </div>

        {{-- Lista de Miembros --}}
        <div class="max-h-60 overflow-y-auto p-1.5 scrollbar-hide">
            <template x-for="member in filteredMembers" :key="member.id">
                <button type="button" 
                    @click="assignUser(member.id)"
                    class="w-full flex items-center justify-between p-2 hover:bg-gray-50 rounded-xl transition-all group"
                    :class="task.assigned_user_id === member.id ? 'bg-gray-50' : ''">
                    <div class="flex items-center gap-3 overflow-hidden text-left">
                        <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold text-xs group-hover:bg-gray-200 group-hover:text-gray-700 transition-colors" 
                             x-text="member.initials"></div>
                        <div class="flex flex-col overflow-hidden">
                            <span class="text-sm font-semibold text-gray-600 group-hover:text-gray-900 truncate" x-text="member.name"></span>
                            <span class="text-[10px] text-gray-400 truncate capitalize" x-text="member.role"></span>
                        </div>
                    </div>
                    <template x-if="task.assigned_user_id === member.id">
                        <x-lucide-check class="w-4 h-4 text-gray-400 mr-2" />
                    </template>
                </button>
            </template>

            {{-- Opción de desasignar --}}
            <div class="border-t border-gray-50 mt-1 pt-1" x-show="task.assigned_user_id">
                <button type="button" 
                    @click="assignUser(null)"
                    class="w-full flex items-center gap-3 p-2 hover:bg-red-50/50 rounded-xl transition-all group text-left">
                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 group-hover:bg-red-100 group-hover:text-red-500 transition-colors">
                        <x-lucide-user-x class="w-4 h-4" />
                    </div>
                    <span class="text-sm font-medium text-gray-500 group-hover:text-red-600">Quitar responsable</span>
                </button>
            </div>

            {{-- Sin resultados --}}
            <template x-if="filteredMembers.length === 0">
                <div class="py-6 text-center">
                    <p class="text-xs text-gray-400 font-medium italic">No se encontraron miembros</p>
                </div>
            </template>
        </div>
    </div>
</div>
