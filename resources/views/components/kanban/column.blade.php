@props([
    'status',
    'label',
    'dotColor',
    'badgeClasses',
    'emptyText' => 'Sin tareas',
])

<div class="flex flex-col h-full rounded-lg p-3 border transition-colors duration-200"
     :class="hoveringColumn === '{{ $status }}' ? 'border-indigo-300 bg-indigo-50/20' : 'border-gray-100/50 bg-gray-50/80'"
     @dragover.prevent="dragOver('{{ $status }}')"
     @drop="drop($event, '{{ $status }}')">

    <div class="flex items-center justify-between mb-3">
        <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
            <div class="w-2.5 h-2.5 rounded-full {{ $dotColor }}"></div>
            {{ $label }}
        </h3>
        <span class="text-xs font-medium px-2.5 py-0.5 rounded-full {{ $badgeClasses }}" x-text="tasksGrouped['{{ $status }}'].length"></span>
    </div>

    <div class="flex flex-col gap-2 flex-1 overflow-y-auto">
        <template x-for="task in tasksGrouped['{{ $status }}']" :key="task.id">
            <x-kanban.card />
        </template>

        <div x-show="tasksGrouped['{{ $status }}'].length === 0" class="py-10 text-center border-2 border-dashed border-gray-200 text-gray-400 text-sm rounded-lg">
            {{ $emptyText }}
        </div>

        @if(in_array($status, ['pending', 'in_progress']))
            <button 
                type="button" 
                @click="$dispatch('open-modal', { name: 'create-task', payload: { status: '{{ $status }}' } })"
                class="flex items-center justify-center gap-2 w-full py-3 bg-gray-50 border-2 border-dashed border-gray-200 rounded-lg text-sm font-medium text-gray-400 hover:text-gray-600 hover:border-gray-300 hover:bg-gray-100 transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
            >
                <x-lucide-plus class="w-4 h-4" />
                <span>Nueva Tarea</span>
            </button>
        @endif
    </div>
</div>
