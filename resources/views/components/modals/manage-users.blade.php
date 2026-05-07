@props(['project' => null])

<x-ui.dialog name="manage-users" max-width="7xl" alpine-data="{
    project: null
}">
    <!-- Header del modal -->
    <x-ui.modal-header :alpine-project-name="'project?.name || \'' . ($project->name ?? '') . '\''" />

    <!-- Cuerpo del modal -->
    <div class="p-8 md:p-12 min-h-[70vh] flex flex-col items-center justify-center text-center">
        <div class="w-16 h-16 bg-orange-50 rounded-full flex items-center justify-center mb-4">
            <x-lucide-users class="w-8 h-8 text-orange-500" />
        </div>
        <h3 class="text-lg font-semibold text-gray-900 mb-1">Gestión de Usuarios</h3>
        <p class="text-sm text-gray-500 max-w-xs">
            Aquí podrás gestionar los miembros del proyecto y sus permisos.
        </p>
    </div>
</x-ui.dialog>
