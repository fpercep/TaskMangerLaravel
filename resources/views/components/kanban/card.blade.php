<div draggable="true"
     tabindex="0"
     @dragstart="startDrag($event, task)"
     @dragend="endDrag()"
     class="relative group bg-white border-transparent border-y-0 border-r-0 border-l-[6px] ring-1 ring-gray-200 rounded-l-md rounded-r-none p-3 shadow-sm cursor-grab active:cursor-grabbing hover:shadow-md transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
     :class="[
         priorityBorderClass(task),
         {
             'opacity-50 scale-95': draggingTaskId === task.id,
             'opacity-80 bg-gray-50': task.status === 'completed'
         }
     ]">
    <!-- Botón de opciones -->
    <div class="absolute top-2 right-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
        <x-ui.icon-button icon="ellipsis-vertical" size="xs" class="text-gray-400 hover:text-gray-600" />
    </div>

    <p class="text-base font-medium pr-6"
       :class="task.status === 'completed' ? 'text-gray-500 line-through' : 'text-gray-800'"
       x-text="task.name"></p>
    <div class="flex items-center gap-1 mt-1 text-xs font-light tracking-wider"
       :class="isOverdue(task) ? 'text-red-500' : 'text-gray-400'">
        <div class="flex items-center gap-1">
            <x-lucide-calendar class="size-3" stroke-width="1.5" />
            <span x-text="formatDate(task.due_date)"></span>
        </div>
        
        <template x-if="task.description">
            <div class="flex items-center gap-1 ml-0.5">
                <div class="h-2.5 w-px bg-current opacity-40"></div>
                <x-lucide-file-text class="size-3" stroke-width="1.5" />
            </div>
        </template>
    </div>
</div>
