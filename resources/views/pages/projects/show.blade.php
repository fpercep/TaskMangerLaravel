<x-app-layout>
    <div class="max-w-full mx-auto h-full flex flex-col">
        {{-- Header del Proyecto --}}
        <div class="mb-6 flex flex-col items-start gap-1">
            <h1 class="text-2xl font-bold text-gray-900">{{ $project->name }}</h1>
            <p class="text-sm text-gray-500 ml-1">
                Creado el {{ $project->created_at->format('d/m/Y') }} 
                @if($project->description)
                <span class="mx-2 text-gray-300">&bull;</span> {{ $project->description }}
                @endif
            </p>
        </div>

        {{-- Cuerpo del Proyecto (Vacío) --}}
        <div class="flex-1 border-2 border-dashed border-gray-200 rounded-xl flex flex-col items-center justify-center text-gray-400 bg-gray-50/50 min-h-[400px]">
             <x-lucide-folder-open class="size-icon-avatar mb-3 text-gray-300" />
             <p class="text-sm font-medium">Contenido del proyecto vacío</p>
             <p class="text-xs text-gray-400 mt-1">Aquí irán las tareas y secciones de este proyecto.</p>
        </div>
    </div>
</x-app-layout>
