<div draggable="true"
     tabindex="0"
     @dragstart="startDrag($event, task)"
     @dragend="endDrag()"
     class="bg-white border-transparent border-y-0 border-r-0 border-l-4 ring-1 ring-gray-200 rounded-l-md rounded-r-none p-3 shadow-sm cursor-grab active:cursor-grabbing hover:shadow-md transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
     :class="[
         priorityBorderClass(task),
         {
             'opacity-50 scale-95': draggingTaskId === task.id,
             'opacity-80 bg-gray-50': task.status === 'completed'
         }
     ]">
    <p class="text-sm font-medium"
       :class="task.status === 'completed' ? 'text-gray-500 line-through' : 'text-gray-800'"
       x-text="task.name"></p>
</div>
