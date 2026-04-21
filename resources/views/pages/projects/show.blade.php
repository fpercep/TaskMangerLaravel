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

        {{-- Cuerpo del Proyecto - Tablero Kanban --}}
        <div class="flex-1 min-h-0 w-full overflow-x-auto flex flex-col"
             x-data="kanbanBoard({{ Js::from($tasks) }}, '{{ route('tasks.update_status', ['task' => ':id']) }}')">

            <div class="min-w-max md:min-w-0 md:w-full flex-1 grid grid-cols-1 md:grid-cols-3 md:grid-rows-1 gap-3 py-2">
                <x-kanban.column
                    status="pending"
                    label="Pendientes"
                    dot-color="bg-gray-400"
                    badge-classes="bg-gray-200 text-gray-600"
                    empty-text="Sin tareas"
                />

                <x-kanban.column
                    status="in_progress"
                    label="En Curso"
                    dot-color="bg-blue-500"
                    badge-classes="bg-blue-100 text-blue-700"
                    empty-text="Mueve una tarea aquí"
                />

                <x-kanban.column
                    status="completed"
                    label="Completadas"
                    dot-color="bg-green-500"
                    badge-classes="bg-green-100 text-green-700"
                    empty-text="Aún no hay tareas"
                />
            </div>
        </div>
    </div>

    {{-- Modals --}}
    <x-modals.create-task :project="$project" />
</x-app-layout>
