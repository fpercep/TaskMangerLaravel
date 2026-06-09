<x-admin-layout title="Administración">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <x-ui.icon-button href="{{ route('dashboard') }}" icon="arrow-left" title="Volver al Dashboard" size="lg" />
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Panel de Administración</h1>
        </div>
    </div>

    <div x-data="adminPanel(@js($users), @js($projects))" class="flex flex-col gap-2">
        
        <!-- Pestañas -->
        <div class="flex items-center gap-4 w-full mb-2">
            <button @click="switchTab('users')" 
                    :class="tab === 'users' ? 'text-black font-bold' : 'text-gray-500 hover:text-black font-semibold'" 
                    class="text-[15px] transition-all flex items-center gap-2 tracking-tight py-1.5">
                Usuarios
            </button>
            
            <div class="w-px h-4 bg-gray-300"></div>

            <button @click="switchTab('projects')" 
                    :class="tab === 'projects' ? 'text-black font-bold' : 'text-gray-500 hover:text-black font-semibold'" 
                    class="text-[15px] transition-all flex items-center gap-2 tracking-tight py-1.5">
                Proyectos
            </button>
        </div>

        <!-- Tabla de Usuarios -->
        <div x-show="tab === 'users'" x-cloak>
            <div class="bg-white overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="py-2 px-4 text-sm font-medium text-gray-400 tracking-normal">Usuario</th>
                                <th class="py-2 px-4 text-sm font-medium text-gray-400 tracking-normal">Rol</th>
                                <th class="py-2 px-4 text-sm font-medium text-gray-400 tracking-normal">Proyectos</th>
                                <th class="py-2 px-4 text-sm font-medium text-gray-400 tracking-normal">Tareas Asignadas</th>
                                <th class="py-2 px-4 text-sm font-medium text-gray-400 tracking-normal">Tareas Pendientes</th>
                                <th class="py-2 px-4 text-sm font-medium text-gray-400 tracking-normal">Tareas Realizadas</th>
                                <th class="py-2 px-4 text-sm font-medium text-gray-400 tracking-normal text-right">Fecha de Registro</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <template x-for="user in filteredUsers" :key="user.id">
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="py-2 px-4">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-sm font-bold text-gray-500 shrink-0">
                                                <span x-text="user.initials"></span>
                                            </div>
                                            <div class="flex flex-col min-w-0">
                                                <span class="text-[15px] font-semibold text-gray-900 truncate leading-snug" x-text="user.name"></span>
                                                <span class="text-[13px] text-gray-500 truncate" x-text="user.email"></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-2 px-4">
                                        <span x-show="user.is_super_admin" class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium bg-gray-50 text-gray-600">
                                            Admin
                                        </span>
                                        <span x-show="!user.is_super_admin" class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium bg-gray-50 text-gray-600">
                                            Usuario
                                        </span>
                                    </td>
                                    <td class="py-2 px-4">
                                        <span class="text-sm text-gray-600" x-text="user.projects_count"></span>
                                    </td>
                                    <td class="py-2 px-4">
                                        <span class="text-sm text-gray-600" x-text="user.tasks_count"></span>
                                    </td>
                                    <td class="py-2 px-4">
                                        <span class="text-sm text-gray-600" x-text="user.pending_tasks_count"></span>
                                    </td>
                                    <td class="py-2 px-4">
                                        <span class="text-sm text-gray-600" x-text="user.completed_tasks_count"></span>
                                    </td>
                                    <td class="py-2 px-4 text-right">
                                        <span class="text-sm text-gray-500" x-text="formatDate(user.created_at)"></span>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="filteredUsers.length === 0">
                                <tr>
                                    <td colspan="7" class="py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-400">
                                            <x-lucide-search-x class="w-12 h-12 mb-3 text-gray-300" />
                                            <p class="text-sm font-medium text-gray-600">No se encontraron usuarios</p>
                                            <p class="text-xs mt-1 text-gray-400">Intenta con otro término de búsqueda</p>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tabla de Proyectos -->
        <div x-show="tab === 'projects'" x-cloak>
            <div class="bg-white overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="py-2 px-4 text-sm font-medium text-gray-400 tracking-normal">Proyecto</th>
                                <th class="py-2 px-4 text-sm font-medium text-gray-400 tracking-normal">Creador</th>
                                <th class="py-2 px-4 text-sm font-medium text-gray-400 tracking-normal">Miembros</th>
                                <th class="py-2 px-4 text-sm font-medium text-gray-400 tracking-normal">Tareas Asignadas</th>
                                <th class="py-2 px-4 text-sm font-medium text-gray-400 tracking-normal">Tareas Pendientes</th>
                                <th class="py-2 px-4 text-sm font-medium text-gray-400 tracking-normal">Tareas Realizadas</th>
                                <th class="py-2 px-4 text-sm font-medium text-gray-400 tracking-normal text-right">Fecha de Creación</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <template x-for="project in filteredProjects" :key="project.id">
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="py-2 px-4">
                                        <div class="flex items-center gap-4">
                                            <div :class="project.color" class="w-3 h-3 rounded-full shrink-0 ring-4 ring-gray-50"></div>
                                            <div class="flex flex-col min-w-0">
                                                <span class="text-[15px] font-semibold text-gray-900 truncate leading-snug" x-text="project.name"></span>
                                                <span class="text-[13px] text-gray-500 truncate max-w-xs" x-text="project.description || 'Sin descripción'"></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-2 px-4">
                                        <template x-if="project.creator">
                                            <div class="flex flex-col min-w-0">
                                                <span class="text-[14px] font-medium text-gray-800 truncate" x-text="project.creator.name"></span>
                                                <span class="text-[13px] text-gray-500 truncate" x-text="project.creator.email"></span>
                                            </div>
                                        </template>
                                        <template x-if="!project.creator">
                                            <span class="text-[14px] text-gray-400 italic">Desconocido</span>
                                        </template>
                                    </td>
                                    <td class="py-2 px-4">
                                        <span class="text-sm text-gray-600" x-text="project.users_count"></span>
                                    </td>
                                    <td class="py-2 px-4">
                                        <span class="text-sm text-gray-600" x-text="project.tasks_count"></span>
                                    </td>
                                    <td class="py-2 px-4">
                                        <span class="text-sm text-gray-600" x-text="project.pending_tasks_count"></span>
                                    </td>
                                    <td class="py-2 px-4">
                                        <span class="text-sm text-gray-600" x-text="project.completed_tasks_count"></span>
                                    </td>
                                    <td class="py-2 px-4 text-right">
                                        <span class="text-sm text-gray-500" x-text="formatDate(project.created_at)"></span>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="filteredProjects.length === 0">
                                <tr>
                                    <td colspan="7" class="py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-400">
                                            <x-lucide-search-x class="w-12 h-12 mb-3 text-gray-300" />
                                            <p class="text-sm font-medium text-gray-600">No se encontraron proyectos</p>
                                            <p class="text-xs mt-1 text-gray-400">Intenta con otro término de búsqueda</p>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</x-admin-layout>
