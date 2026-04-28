@props(['project'])

<div x-data="{ 
        show: false, 
        task: {},
        handleOpen(e) {
            this.task = e.detail;
            this.show = true;
        }
     }"
     x-show="show"
     @open-task-details.window="handleOpen($event)"
     @keydown.escape.window="show = false"
     class="fixed inset-0 z-[100] overflow-y-auto"
     style="display: none;">
     
    <!-- Backdrop difuminado -->
    <div x-show="show"
         class="fixed inset-0 bg-gray-900/40 backdrop-blur-md transition-opacity"
         @click="show = false"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"></div>

    <!-- Modal Content -->
    <div x-show="show"
         class="relative flex min-h-screen items-center justify-center p-4 sm:p-6"
         @click.self="show = false"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
        
        <div class="w-full max-w-7xl bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-200 ring-1 ring-black/5 transform transition-all">
            <!-- Header del modal -->
            <div class="px-5 py-2 border-b border-gray-100 flex items-center justify-between bg-gray-50/30">
                <div class="flex items-center gap-2 text-xs uppercase tracking-widest font-medium text-gray-400">
                    <x-lucide-folder class="w-4 h-4" />
                    <span>{{ $project->name }}</span>
                </div>
                
                <button @click="show = false" class="text-gray-400 hover:text-gray-600 transition-colors p-1.5 rounded-lg hover:bg-gray-100 focus:outline-none">
                    <x-lucide-x class="w-5 h-5" />
                </button>
            </div>
            
            <!-- Cuerpo del modal -->
            <div class="p-8 md:p-12 min-h-[70vh]">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 tracking-tight" x-text="task.name"></h2>
                
                <!-- El resto del contenido irá aquí en el futuro -->
            </div>
        </div>
    </div>
</div>

