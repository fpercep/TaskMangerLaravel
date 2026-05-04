<div draggable="true"
     tabindex="0"
     @dragstart="startDrag($event, task)"
     @dragend="endDrag()"
     @click="$dispatch('open-task-details', task)"
     class="relative group bg-white border-l-accent border-solid border-transparent ring-1 ring-gray-200 rounded-md p-3 shadow-sm cursor-pointer hover:shadow-md transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
     :class="[
         priorityBorderClass(task),
         {
             'opacity-50 scale-95': draggingTaskId === task.id,
             'opacity-80 bg-gray-50': task.status === 'completed',
             'ring-2 ring-orange-500 border-l-orange-500 shadow-lg z-20': renamingTaskId === task.id
         }
     ]">
    <!-- Menú de opciones (3 puntos) -->
    <div class="absolute top-2 right-1 z-10" x-data="{ openMenu: false }">
        <div class="transition-opacity duration-200" :class="openMenu ? 'opacity-100' : 'opacity-0 group-hover:opacity-100'">
            <x-ui.icon-button 
                @click.stop="openMenu = !openMenu" 
                icon="ellipsis-vertical" 
                size="xs" 
                class="text-gray-400 hover:text-gray-600 transition-colors" 
                x-bind:class="openMenu ? 'bg-gray-200 text-gray-600' : ''" 
            />
        </div>

        <!-- Dropdown Contextual -->
        <x-ui.context-menu>
            <x-ui.dropdown-item icon="type" @click.stop="openMenu = false; renameTask(task)">
                Renombrar
            </x-ui.dropdown-item>

            <x-ui.dropdown-item icon="pencil" @click.stop="openMenu = false; $dispatch('open-task-details', task)">
                Editar
            </x-ui.dropdown-item>
            
            <x-ui.dropdown-item icon="copy" @click.stop="openMenu = false; duplicateTask(task)">
                Duplicar
            </x-ui.dropdown-item>

            <div class="h-px bg-gray-100 my-1"></div>
            
            <x-ui.dropdown-item icon="trash-2" :destructive="true" @click.stop="openMenu = false; $dispatch('open-modal', { name: 'delete-task', payload: { task } })">
                Eliminar
            </x-ui.dropdown-item>
        </x-ui.context-menu>
    </div>

    <template x-if="renamingTaskId !== task.id">
        <p class="text-base font-medium pr-6"
           :class="task.status === 'completed' ? 'text-gray-500 line-through' : 'text-gray-800'"
           x-text="task.name"></p>
    </template>

    <template x-if="renamingTaskId === task.id">
        <div class="pr-6 mb-1.5" x-data="{ currentName: task.name }">
            <input 
                type="text" 
                x-model="currentName"
                x-init="$nextTick(() => { $el.focus(); $el.select(); })"
                @keyup.enter="updateTaskName(task, currentName)"
                @keyup.escape="cancelRenaming()"
                @blur="updateTaskName(task, currentName)"
                @click.stop
                class="w-full text-base font-medium text-gray-900 border-none px-2 py-1 focus:ring-0 rounded transition-all duration-200"
            >
        </div>
    </template>
    <div class="flex items-center gap-1 mt-1 text-xs font-light tracking-wider"
       :class="isOverdue(task) ? 'text-red-500' : 'text-gray-400'">
        <div class="flex items-center gap-1">
            <x-lucide-calendar class="size-3" stroke-width="1.5" />
            <span x-text="$formatDate(task.due_date)"></span>
        </div>
        
        <template x-if="task.has_description">
            <div class="flex items-center gap-1 ml-0.5">
                <div class="h-2.5 w-px bg-current opacity-40"></div>
                <x-lucide-file-text class="size-3" stroke-width="1.5" />
            </div>
        </template>

        <template x-if="task.steps_count > 0">
            <x-tasks.steps-indicator 
                completed="task.completed_steps_count" 
                total="task.steps_count" 
            />
        </template>
    </div>
</div>
