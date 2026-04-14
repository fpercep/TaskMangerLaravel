<div x-data="{ show: false }" 
     @open-create-project-modal.window="show = true; $nextTick(() => $refs.projectName.focus())"
     x-show="show" 
     style="display: none;"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0">

    <!-- Fondo desenfocado (Backdrop) -->
    <div x-show="show" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="show = false"
         class="fixed inset-0 bg-gray-900/20 backdrop-blur-sm transition-opacity"></div>

    <!-- Modal Card (Simulando la estética limpia del dropmenu de ajustes) -->
    <div x-show="show" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         class="relative bg-white rounded-dropdown shadow-[0_10px_35px_rgb(0,0,0,0.06)] border border-gray-100 w-full max-w-md transform transition-all overflow-hidden z-10">
        
        <!-- Header -->
        <div class="flex items-center justify-between px-5 pt-5 pb-4 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-800">Crea un nuevo proyecto</h3>
            <button @click="show = false" type="button" class="text-gray-400 hover:text-gray-700 hover:bg-gray-50 p-1.5 rounded-btn transition-colors focus:outline-none">
                <x-lucide-x class="size-icon-sm" />
            </button>
        </div>

        <!-- Formulario central -->
        <form action="{{ route('projects.store') }}" method="POST" class="p-5">
            @csrf
            
            <div class="mb-4">
                <label for="project_name" class="block text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wider">
                    Nombre del proyecto
                </label>
                <input type="text" id="project_name" name="name" x-ref="projectName" required placeholder="Ej: Rediseño Landing"
                    class="w-full text-sm placeholder:text-gray-400 bg-gray-50 border border-transparent focus:border-orange-300 focus:bg-white focus:ring-2 focus:ring-orange-100 rounded-md p-3 transition-colors outline-none shadow-[inset_0_1px_2px_rgba(0,0,0,0.02)]">
            </div>

            <div class="mb-5">
                <label for="project_description" class="block text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wider">
                    Descripción <span class="text-gray-300 font-normal normal-case">(Opcional)</span>
                </label>
                <textarea id="project_description" name="description" rows="2" placeholder="Agrega un pequeño contexto al proyecto..."
                    class="w-full text-sm placeholder:text-gray-400 bg-gray-50 border border-transparent focus:border-orange-300 focus:bg-white focus:ring-2 focus:ring-orange-100 rounded-md p-3 transition-colors outline-none resize-none shadow-[inset_0_1px_2px_rgba(0,0,0,0.02)]"></textarea>
            </div>
            
            <!-- Botonera -->
            <div class="flex items-center gap-3 justify-center pt-2">
                <button type="button" @click="show = false" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-md transition-colors focus:outline-none">
                    Cancelar
                </button>
                <button type="submit" class="bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium py-2 px-6 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">
                    Crear Proyecto
                </button>
            </div>
        </form>

    </div>
</div>
