<div x-data="{ 
        show: false, 
        project: null,
        openModal(projectData) {
            this.show = true;
            this.project = projectData;
        },
        deleteUrl() {
            return `{{ url('projects') }}/${this.project?.id}`;
        }
     }" 
     @open-delete-project-modal.window="openModal($event.detail.project)"
     x-show="show" 
     style="display: none;"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0">

    <div x-show="show" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="show = false"
         class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity"></div>

    <div x-show="show" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         class="relative bg-white rounded-xl shadow-2xl w-full max-w-sm transform transition-all overflow-hidden z-10 text-center">
        
        <div class="px-6 pt-8 pb-6">
            <div class="mx-auto flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-full bg-rose-100 mb-4">
                <i data-lucide="alert-triangle" class="h-7 w-7 text-rose-600"></i>
            </div>
            
            <h3 class="text-base font-bold text-gray-900 mb-2">¿Eliminar proyecto?</h3>
            <p class="text-sm text-gray-500">
                Se eliminará permanentemente "<span class="font-semibold text-gray-700" x-text="project?.name"></span>" y todas sus tareas. Esta acción no se puede deshacer.
            </p>
        </div>

        <form :action="deleteUrl()" method="POST" class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row-reverse sm:gap-3">
            @csrf
            @method('DELETE')
            <button type="submit" class="w-full inline-flex justify-center rounded-md bg-rose-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 sm:ml-3 sm:w-auto transition-colors">
                Sí, eliminar
            </button>
            <button type="button" @click="show = false" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 sm:mt-0 sm:w-auto transition-colors">
                Cancelar
            </button>
        </form>

    </div>
</div>
