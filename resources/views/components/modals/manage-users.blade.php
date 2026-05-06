@props(['project' => null])

<x-ui.dialog name="manage-users" max-width="2xl" alpine-data="{
    project: null
}">
    <!-- Header del modal (Estilo del Referente de Tareas) -->
    <div class="px-5 py-2 border-b border-gray-100 flex items-center justify-between bg-gray-50/30">
        <div class="flex items-center gap-2 text-xs uppercase tracking-widest font-medium text-gray-400">
            <x-lucide-folder class="w-4 h-4" />
            <!-- Usamos x-text para que sea reactivo al proyecto seleccionado en el sidebar -->
            <span x-text="project?.name || '{{ $project->name ?? '' }}'"></span>
        </div>

        <button @click="show = false"
            class="text-gray-400 hover:text-gray-600 transition-colors p-1.5 rounded-lg hover:bg-gray-100 focus:outline-none">
            <x-lucide-x class="w-5 h-5" />
        </button>
    </div>

    <!-- Cuerpo del modal -->
    <div class="p-8 min-h-[400px] flex flex-col items-center justify-center text-center">
        <div class="w-16 h-16 bg-orange-50 rounded-full flex items-center justify-center mb-4">
            <x-lucide-users class="w-8 h-8 text-orange-500" />
        </div>
        <h3 class="text-lg font-semibold text-gray-900 mb-1">Gestión de Usuarios</h3>
        <p class="text-sm text-gray-500 max-w-xs">
            Aquí podrás gestionar los miembros del proyecto y sus permisos.
        </p>
    </div>
</x-ui.dialog>
