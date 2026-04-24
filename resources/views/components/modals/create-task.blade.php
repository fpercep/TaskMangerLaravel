@props(['project'])

<x-ui.dialog name="create-task" max-width="lg" alpine-data="{
    status: 'pending',
    onOpen() {
        this.$nextTick(() => this.$refs.taskName.focus());
    }
}">
    <div class="bg-white">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900 tracking-tight">Nueva tarea</h3>
            <button @click="show = false" type="button" class="text-gray-400 hover:text-gray-700 hover:bg-gray-100 p-2 rounded-full transition-colors focus:outline-none">
                <x-lucide-x class="w-5 h-5" />
            </button>
        </div>

        <!-- Formulario compacto -->
        <form action="{{ route('tasks.store', $project) }}" method="POST">
            @csrf
            
            <div class="px-6 py-6 space-y-5">
                <div class="space-y-4">
                    <div>
                        <x-ui.input-label for="task_name" value="Nombre" />
                        <x-ui.text-input id="task_name" name="name" x-ref="taskName" required placeholder="Ej: Rediseñar vista de facturación" class="w-full text-sm bg-gray-50 border-gray-200 focus:bg-white focus:border-orange-500 focus:ring-orange-500" />
                    </div>
                    <div>
                        <x-ui.input-label for="task_description" value="Descripción" />
                        <x-ui.textarea id="task_description" name="description" rows="3" placeholder="Añade detalles..." class="w-full text-sm bg-gray-50 border-gray-200 focus:bg-white focus:border-orange-500 focus:ring-orange-500"></x-ui.textarea>
                    </div>
                </div>

                <div class="space-y-4">
                    <x-ui.input-label value="Estado" />
                    <div class="flex items-center gap-6">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="radio" name="status" value="pending" x-model="status" class="h-4 w-4 border-gray-300 text-orange-600 focus:ring-orange-600 focus:ring-offset-1 transition-colors">
                            <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">Pendiente</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="radio" name="status" value="in_progress" x-model="status" class="h-4 w-4 border-gray-300 text-orange-600 focus:ring-orange-600 focus:ring-offset-1 transition-colors">
                            <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">En Curso</span>
                        </label>
                    </div>
                </div>

                <x-ui.priority-slider name="priority" :value="1" />

                <div>
                    <x-ui.input-label for="task_due_date" value="Fecha límite" />
                    <x-ui.text-input id="task_due_date" type="date" name="due_date" class="w-full text-sm text-gray-700 bg-gray-50 border-gray-200 focus:bg-white focus:border-orange-500 focus:ring-orange-500" />
                </div>
            </div>

            <!-- Botonera -->
            <div class="flex items-center justify-end px-6 py-4 bg-gray-50/50 border-t border-gray-100 gap-3">
                <button type="button" @click="show = false" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 rounded-lg transition-colors">
                    Cancelar
                </button>
                <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white text-sm font-semibold py-2 px-6 rounded-lg shadow-sm transition-colors flex items-center gap-2">
                    Crear Tarea
                </button>
            </div>
        </form>
    </div>
</x-ui.dialog>
