@props(['project' => null])

<x-ui.dialog name="manage-users" max-width="2xl" alpine-data="{
    project: null
}">
    <!-- Header del modal -->
    <x-ui.modal-header :alpine-project-name="'project?.name || \'' . ($project->name ?? '') . '\''" />

    <!-- Cuerpo del modal -->
    <div class="p-6 md:p-8 min-h-[85vh] flex flex-col" 
         x-data="projectMembers()"
         x-init="projectId = project?.id; currentUserId = {{ auth()->id() }}; $watch('project', val => projectId = val?.id)"
         @user-selected.stop="handleUserSelected($event.detail.userId)">
        
        <!-- Sub-header de Miembros -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex flex-col">
                <h2 class="text-xl font-bold text-gray-900 tracking-tighter">
                    Miembros del proyecto
                </h2>
                <p class="text-sm text-gray-400 font-medium">
                    <span x-text="members.length">0</span> miembros
                </p>
            </div>

            <div class="relative" x-data="userSearch()">
                <button type="button" 
                    @click="showPanel = !showPanel" 
                    class="group flex items-center gap-1.5 text-sm font-bold text-white bg-blue-500 hover:bg-blue-600 rounded-lg px-4 py-2 transition-all duration-200 shadow-sm hover:shadow-md focus:outline-none">
                    <x-lucide-plus class="w-4 h-4 text-white group-hover:scale-110 transition-transform" />
                    <span>Añadir miembro</span>
                </button>

                <!-- Bloque Flotante de Búsqueda -->
                <div x-show="showPanel" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-[-10px]"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 translate-y-[-10px]"
                     @click.away="showPanel = false"
                     class="absolute right-0 mt-3 w-80 bg-white rounded-2xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.15)] border border-gray-100 z-50 overflow-hidden"
                     style="display: none;">
                    
                    <div class="p-4 border-b border-gray-50 bg-gray-50/30">
                        <div class="relative group">
                            <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-blue-500 transition-colors" />
                            <input type="text" 
                                x-model="query"
                                placeholder="Buscar por nombre o email..." 
                                class="w-full bg-white border border-gray-200 rounded-xl pl-9 pr-3 py-2 text-sm placeholder:text-gray-400 focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition-all outline-none">
                        </div>
                    </div>

                    <div class="max-h-64 overflow-y-auto p-2 scrollbar-hide">
                        <!-- Cargando -->
                        <div x-show="isSearching" class="py-8 flex justify-center">
                            <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-500"></div>
                        </div>

                        <div x-show="!isSearching">
                            <!-- Resultados -->
                            <template x-for="user in results" :key="user.id">
                                <div class="w-full flex items-center justify-between p-2 hover:bg-blue-50/50 rounded-xl transition-colors group">
                                    <div class="flex items-center gap-3 overflow-hidden">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs group-hover:bg-blue-200 transition-colors" x-text="user.initials"></div>
                                        <div class="flex flex-col overflow-hidden">
                                            <span class="text-sm font-semibold text-gray-900 truncate" x-text="user.name"></span>
                                            <span class="text-xs text-gray-400 truncate" x-text="user.email"></span>
                                        </div>
                                    </div>

                                    {{-- Botón añadir (solo si no es miembro) --}}
                                    <template x-if="!Alpine.store('members').members.some(m => m.id === user.id)">
                                        <button type="button" 
                                            @click="selectUser(user.id)"
                                            class="p-2 text-gray-400 hover:text-blue-500 hover:bg-blue-100 rounded-lg transition-all"
                                            title="Añadir al proyecto">
                                            <x-lucide-plus class="w-4 h-4" />
                                        </button>
                                    </template>

                                    {{-- Indicador de ya miembro --}}
                                    <template x-if="Alpine.store('members').members.some(m => m.id === user.id)">
                                        <span class="px-2 py-1 text-[10px] font-bold text-gray-300 uppercase tracking-tighter">Miembro</span>
                                    </template>
                                </div>
                            </template>

                            <!-- Sin parámetros de búsqueda -->
                            <template x-if="query.trim().length < 2 && !isSearching">
                                <div class="py-8 text-center">
                                    <p class="text-sm text-gray-400 font-medium italic">Sin parámetros de búsqueda</p>
                                    <p class="text-xs text-gray-300 mt-1">Escribe al menos 2 caracteres para empezar</p>
                                </div>
                            </template>

                            <!-- Ningún usuario encontrado -->
                            <template x-if="query.trim().length >= 2 && results.length === 0 && !isSearching">
                                <div class="py-8 text-center">
                                    <p class="text-sm text-gray-400 font-medium italic">Ningún usuario encontrado</p>
                                    <p class="text-xs text-gray-300 mt-1">No hay coincidencias para "<span class="text-gray-500" x-text="query"></span>"</p>
                                </div>
                            </template>
                        </div> {{-- Fin !isSearching --}}
                    </div> {{-- Fin max-h-64 --}}
                </div> {{-- Fin showPanel --}}
            </div> {{-- Fin relative --}}
        </div> {{-- Fin header flex --}}

        <!-- Filtros Rápidos -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 mb-6 scrollbar-hide">
            @foreach(['Todos', 'Admin', 'Manager', 'Editor'] as $role)
                <button type="button" 
                    @click="activeFilter = '{{ $role }}'"
                    :class="activeFilter === '{{ $role }}' 
                        ? 'bg-blue-500 text-white font-bold shadow-sm shadow-blue-100' 
                        : 'text-gray-400 font-medium hover:text-gray-700 hover:bg-gray-100'"
                    class="px-4 py-1.5 text-xs rounded-lg transition-all whitespace-nowrap">
                    {{ $role }}
                </button>
            @endforeach
        </div>

        <!-- Tabla de Miembros (Diseño Abierto) -->
        <div class="flex-1 overflow-auto -mx-2 px-2 scrollbar-hide flex flex-col">
            <div x-show="isBusy" class="py-12 flex justify-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
            </div>

            <table x-show="!isBusy" class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="py-3 pl-4 pr-3 w-10">
                            <input type="checkbox" 
                                @change="toggleAll($event.target.checked)"
                                :checked="selected.length === filteredMembers.length && filteredMembers.length > 0"
                                class="rounded border-gray-200 text-blue-600 focus:ring-blue-100 transition-all cursor-pointer">
                        </th>
                        <th class="py-3 px-3 text-xs font-medium text-gray-400">Usuario</th>
                        <th class="py-3 px-3 text-xs font-medium text-gray-400 w-28">Rol</th>
                        <th class="py-3 px-3 pr-4 text-xs font-medium text-gray-400 w-24 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <template x-for="member in filteredMembers" :key="member.id">
                        <tr class="group hover:bg-gray-50/50 transition-colors" :class="selected.includes(member.id) ? 'bg-blue-50/30' : ''">
                            <td class="py-3 pl-4 pr-3">
                                <input type="checkbox" 
                                    :value="member.id"
                                    x-model="selected"
                                    :disabled="isProtected(member)"
                                    :class="isProtected(member) ? 'opacity-20 cursor-default' : 'cursor-pointer'"
                                    class="rounded border-gray-200 text-blue-600 focus:ring-blue-100 transition-all">
                            </td>
                            <td class="py-3 px-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs" x-text="member.name.charAt(0)"></div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-gray-900" x-text="member.name"></span>
                                        <span class="text-xs text-gray-400" x-text="member.email"></span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-3">
                                <div x-data="{ localRole: member.role }">
                                    {{-- Caso: No editable (Tú o el Admin) --}}
                                    <template x-if="isProtected(member)">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 capitalize" x-text="member.role"></span>
                                    </template>
                                    
                                    {{-- Caso: Editable --}}
                                    <template x-if="!isProtected(member)">
                                        <select x-model="localRole" 
                                                @change="updateRole(member.id, $event.target.value)" 
                                                class="text-xs border-transparent bg-gray-50 hover:bg-gray-100 rounded-lg px-2 py-1 focus:ring-2 focus:ring-blue-100 cursor-pointer capitalize font-semibold text-gray-600 transition-colors outline-none appearance-none">
                                            <option value="admin" x-show="false">Admin</option>
                                            <option value="manager" :selected="member.role === 'manager'">Manager</option>
                                            <option value="editor" :selected="member.role === 'editor'">Editor</option>
                                        </select>
                                    </template>
                                </div>
                            </td>
                            <td class="py-3 px-3 pr-4 text-right">
                                <button @click="removeMember(member.id)" 
                                    :disabled="isProtected(member)"
                                    :class="isProtected(member) ? 'opacity-20 cursor-default' : 'text-gray-400 hover:text-red-500 cursor-pointer'"
                                    class="transition-colors p-1">
                                    <x-lucide-trash-2 class="w-4 h-4" />
                                </button>
                            </td>
                        </tr>
                    </template>

                    <template x-if="filteredMembers.length === 0">
                        <tr>
                            <td colspan="4" class="py-12 text-center text-sm text-gray-400">
                                No se encontraron miembros.
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</x-ui.dialog>
