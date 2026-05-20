<x-ui.dialog name="leave-project" max-width="sm" centered="true" alpine-data="{
    project: null,
    leaveUrl() {
        return `{{ url('projects') }}/${this.project?.id}/leave`;
    }
}">
    <div class="px-6 pt-8 pb-6 flex flex-col items-center">
        <div class="mx-auto flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-full bg-amber-100 mb-4">
            <x-lucide-log-out class="h-7 w-7 text-amber-600" />
        </div>
        
        <h3 class="text-base font-bold text-gray-900 mb-2">¿Abandonar proyecto?</h3>
        <p class="text-sm text-gray-500 text-center">
            Dejarás de ser miembro de "<span class="font-semibold text-gray-700" x-text="project?.name"></span>". 
            Perderás acceso al proyecto y tus tareas asignadas serán desasignadas.
        </p>
    </div>

    <form :action="leaveUrl()" method="POST" class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row justify-center sm:gap-3">
        @csrf
        <button type="submit" class="w-full inline-flex justify-center rounded-md bg-amber-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 sm:w-auto transition-colors">
            Sí, abandonar
        </button>
        <button type="button" @click="show = false" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 sm:mt-0 sm:w-auto transition-colors">
            Cancelar
        </button>
    </form>
</x-ui.dialog>
