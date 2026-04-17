<x-app-layout :title="'Proyecto - ' . $project->name">
    <div class="max-w-full mx-auto h-full flex flex-col">
        {{-- Header del Proyecto --}}
        <div class="mb-10 flex flex-col items-start">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight leading-none mb-3">{{ $project->name }}</h1>
            
            <div class="ml-0.5">
                @if($project->description)
                    <p class="text-sm text-gray-600 max-w-2xl leading-relaxed font-medium">
                        {{ $project->description }}
                    </p>
                @endif
            </div>
        </div>

        {{-- Cuerpo del Proyecto (Vacío) --}}
        <div class="flex-1 border-2 border-dashed border-gray-200 rounded-2xl flex flex-col items-center justify-center text-gray-400 bg-gray-50/30 min-h-[400px] group hover:bg-gray-50/60 hover:border-gray-300 transition-all duration-300">
             <div class="w-20 h-20 bg-gray-100 rounded-3xl flex items-center justify-center mb-5 group-hover:scale-110 group-hover:bg-white group-hover:shadow-sm transition-all duration-500">
                 <x-lucide-folder-open class="size-8 text-gray-300 group-hover:text-gray-400 transition-colors" />
             </div>
             <p class="text-lg font-bold text-gray-500 mb-2">Contenido del proyecto vacío</p>
             <p class="text-sm text-gray-400 text-center max-w-xs">Organiza este proyecto añadiendo tareas, secciones y plazos para tu equipo.</p>
        </div>
    </div>
</x-app-layout>
