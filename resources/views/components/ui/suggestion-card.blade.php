@props(['tarea'])

<div class="group flex items-center justify-between p-3 bg-gray-50 hover:bg-white rounded-lg border border-transparent hover:border-gray-200 hover:shadow-sm transition-all cursor-pointer">
    <div class="flex-1 min-w-0">
        <p class="text-sm font-medium text-gray-800 truncate">
            {{ $tarea['titulo'] }}
        </p>
        <p class="text-xs text-gray-500 mt-0.5">
            <span class="text-orange-500">
                {{ $tarea['proyecto'] }}
            </span> ·
            {{ $tarea['fecha'] }}
        </p>
    </div>
    <button
        class="opacity-0 group-hover:opacity-100 p-1.5 text-orange-400 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition-all">
        <x-lucide-plus class="size-icon-sm" />
    </button>
</div>
