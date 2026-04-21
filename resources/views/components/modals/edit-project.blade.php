<x-ui.dialog name="edit-project" max-width="md" alpine-data="{
    project: null,
    onOpen() {
        this.$nextTick(() => this.$refs.editProjectName.focus());
    },
    updateUrl() {
        return `{{ url('projects') }}/${this.project?.id}`;
    }
}">
    <!-- Header -->
    <div class="flex items-center justify-between px-5 pt-5 pb-4 border-b border-gray-100">
        <h3 class="text-sm font-bold text-gray-800">Editar proyecto</h3>
        <button @click="show = false" type="button" class="text-gray-400 hover:text-gray-700 hover:bg-gray-50 p-1.5 rounded-btn transition-colors focus:outline-none">
            <x-lucide-x class="size-icon-sm" />
        </button>
    </div>

    <!-- Formulario central dinámico -->
    <form :action="updateUrl()" method="POST" class="p-5">
        @csrf
        @method('PUT')
        
        <div class="mb-4">
            <x-ui.input-label for="edit_project_name" value="Nombre del proyecto" />
            <x-ui.text-input id="edit_project_name" name="name" x-model="project.name" x-ref="editProjectName" required />
        </div>

        <div class="mb-5">
            <x-ui.input-label for="edit_project_description">
                Descripción <span class="text-gray-300 font-normal normal-case">(Opcional)</span>
            </x-ui.input-label>
            <x-ui.textarea id="edit_project_description" name="description" x-model="project.description" rows="2"></x-ui.textarea>
        </div>
        
        <!-- Botonera -->
        <div class="flex items-center gap-3 justify-center pt-2">
            <button type="button" @click="show = false" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-md transition-colors focus:outline-none">
                Cancelar
            </button>
            <button type="submit" class="bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium py-2 px-6 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">
                Guardar Cambios
            </button>
        </div>
    </form>
</x-ui.dialog>
