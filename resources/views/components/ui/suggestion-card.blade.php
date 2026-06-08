@props(['source', 'dueColorClass' => ''])

<div class="group/card flex items-center justify-between p-3 bg-gray-50 hover:bg-white rounded-lg border border-transparent hover:border-gray-200 hover:shadow-sm transition-all cursor-pointer">
    <div class="flex-1 min-w-0">
        <p class="text-sm font-medium text-gray-800 truncate" x-text="tarea.name"></p>
        <p class="text-xs text-gray-500 mt-0.5">
            <span class="text-orange-500" x-text="tarea.project_name"></span> ·
            <span class="{{ $dueColorClass }}" x-text="tarea.due_date_fmt"></span>
        </p>
    </div>
    <button @click="addToMyDay(tarea, '{{ $source }}')"
            class="opacity-0 group-hover/card:opacity-100 p-1.5 text-orange-400 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition-all"
            title="Añadir a Mi Día">
        <x-lucide-plus class="size-icon-sm" />
    </button>
</div>
