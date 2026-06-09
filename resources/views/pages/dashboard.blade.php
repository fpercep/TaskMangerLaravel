<x-app-layout>
    <div class="max-w-full mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        </div>

        <!-- Banner Mi Día -->
        <div class="bg-white border border-gray-200 rounded-2xl p-4 md:p-5 mb-8 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-gray-900 mb-1">Resumen de Mi Día</h2>
                <p class="text-sm text-gray-600">
                    Tienes <strong class="text-gray-900">{{ $activeTasksCount }}</strong> tareas activas.
                </p>
            </div>
            <a href="{{ route('mi-dia') }}" class="group inline-flex items-center justify-center gap-2 bg-blue-50 text-blue-600 font-bold py-2 px-5 rounded-xl hover:bg-blue-100 transition-all text-sm">
                Ir a Mi Día
                <x-lucide-arrow-right class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
            </a>
        </div>


        <!-- Tareas Asignadas -->
        <div class="mb-8"
             x-data="dashboardTasks(@js($assignedTasks))"
             @task-created.window="handleRealtimeUpdate($event.detail.task)"
             @task-updated.window="handleRealtimeUpdate($event.detail.task)"
             @task-assigned.window="handleRealtimeUpdate($event.detail.task)"
             @task-steps-updated.window="handleRealtimeUpdate($event.detail.task)"
             @task-deleted.window="handleRealtimeDelete($event.detail.task_id)">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                <h2 class="text-xl font-bold text-gray-900">Mis Tareas</h2>
                
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Búsqueda -->
                    <div class="relative">
                        <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                        <input type="text" x-model="search" placeholder="Buscar tarea..." class="pl-9 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-300 outline-none w-full sm:w-64 transition-all">
                    </div>
                    
                    <!-- Filtros Rápidos -->
                    <div class="flex bg-gray-100 p-1 rounded-xl">
                        <button @click="filter = 'all'" :class="filter === 'all' ? 'bg-white shadow-sm font-semibold text-gray-900' : 'text-gray-500 hover:text-gray-700'" class="px-3 py-1.5 rounded-lg text-sm transition-all">Todas</button>
                        <button @click="filter = 'pending'" :class="filter === 'pending' ? 'bg-white shadow-sm font-semibold text-gray-900' : 'text-gray-500 hover:text-gray-700'" class="px-3 py-1.5 rounded-lg text-sm transition-all">Pendientes</button>
                        <button @click="filter = 'in_progress'" :class="filter === 'in_progress' ? 'bg-white shadow-sm font-semibold text-gray-900' : 'text-gray-500 hover:text-gray-700'" class="px-3 py-1.5 rounded-lg text-sm transition-all">En Progreso</button>
                    </div>
                </div>
            </div>

            <div class="flex-1 overflow-x-auto -mx-2 px-2 scrollbar-hide max-h-[32rem]">
                <table class="w-full text-left border-collapse">
                    <thead class="sticky top-0 bg-white z-10">
                        <tr class="border-b border-gray-50">
                            <!-- Accesibilidad: text-gray-600 en lugar de 400 -->
                                <th class="py-4 px-2 text-sm font-semibold text-gray-600">Nombre</th>
                            <th class="py-4 px-2 text-sm font-semibold text-gray-600 w-32">Estado</th>
                            <th class="py-4 px-2 text-sm font-semibold text-gray-600 w-32">Prioridad</th>
                            <th class="py-4 px-2 text-sm font-semibold text-gray-600 w-32">Vencimiento</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="task in filteredTasks" :key="task.id">
                            <tr @click="goToTask(task)" class="group hover:bg-gray-50/30 transition-colors cursor-pointer">
                                <td class="py-4 px-2">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-gray-900" x-text="task.name"></span>
                                        <!-- Accesibilidad: text-gray-500 en lugar de 400 -->
                                        <span class="text-xs text-gray-500" x-text="task.project ? task.project.name : 'Sin proyecto'"></span>
                                    </div>
                                </td>
                                <td class="py-4 px-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium capitalize"
                                          :class="{
                                              'bg-green-100 text-green-700': task.status === 'completed',
                                              'bg-blue-100 text-blue-700': task.status === 'in_progress',
                                              'bg-gray-100 text-gray-700': task.status === 'cancelled',
                                              'bg-gray-100 text-gray-700': task.status === 'pending'
                                          }"
                                          x-text="statusLabel(task.status)">
                                    </span>
                                </td>
                                <td class="py-4 px-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium capitalize"
                                          :class="{
                                              'bg-red-100 text-red-700': task.priority === 'urgent',
                                              'bg-orange-100 text-orange-700': task.priority === 'high',
                                              'bg-yellow-100 text-yellow-800': task.priority === 'medium',
                                              'bg-blue-100 text-blue-700': task.priority === 'low'
                                          }"
                                          x-text="priorityLabel(task.priority)">
                                    </span>
                                </td>
                                <td class="py-4 px-2">
                                    <!-- Corrección de Lógica de Fecha: Solo rojo si es pasada estrictamente -->
                                    <template x-if="task.due_date">
                                        <span class="text-sm" :class="isOverdue(task) ? 'text-red-500' : 'text-gray-600'" x-text="formatDate(task.due_date)"></span>
                                    </template>
                                    <template x-if="!task.due_date">
                                        <span class="text-sm text-gray-400">-</span>
                                    </template>
                                </td>
                            </tr>
                        </template>

                        <template x-if="filteredTasks.length === 0">
                            <tr>
                                <td colspan="4" class="py-12 text-center text-sm text-gray-400">
                                    No se encontraron tareas con esos filtros.
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>


        </div>
    </div>
</x-app-layout>