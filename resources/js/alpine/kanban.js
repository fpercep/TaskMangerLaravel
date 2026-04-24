const PRIORITY_BORDER_ACTIVE = {
    'low': 'border-l-priority-low',
    'medium': 'border-l-priority-medium',
    'high': 'border-l-priority-high',
    'urgent': 'border-l-priority-urgent',
};

const PRIORITY_BORDER_COMPLETED = {
    'low': 'border-l-priority-low/30',
    'medium': 'border-l-priority-medium/30',
    'high': 'border-l-priority-high/30',
    'urgent': 'border-l-priority-urgent/30',
};

export default () => {
    Alpine.data('kanbanBoard', (initialTasks, updateStatusUrl) => ({
        tasks: initialTasks,
        draggingTaskId: null,
        hoveringColumn: null,

        get tasksGrouped() {
            return this.tasks.reduce((groups, task) => {
                if (!groups[task.status]) groups[task.status] = [];
                groups[task.status].push(task);
                return groups;
            }, { 'pending': [], 'in_progress': [], 'completed': [] });
        },

        formatDate(dateString) {
            if (!dateString) return 'xx/xx/xxxx';
            
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return 'xx/xx/xxxx';
            
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            
            return `${day}/${ month}/${ year}`;
        },

        isOverdue(task) {
            if (!task.due_date || task.status === 'completed') return false;
            const dueDate = new Date(task.due_date);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            return dueDate < today;
        },

        priorityBorderClass(task) {
            if (!task.priority) return 'border-l-gray-300';
            const map = task.status === 'completed' ? PRIORITY_BORDER_COMPLETED : PRIORITY_BORDER_ACTIVE;
            return map[task.priority] || 'border-l-gray-300';
        },

        startDrag(event, task) {
            this.draggingTaskId = task.id;
            event.dataTransfer.effectAllowed = 'move';
        },

        endDrag() {
            this.draggingTaskId = null;
            this.hoveringColumn = null;
        },

        dragOver(status) {
            if (this.hoveringColumn !== status) {
                this.hoveringColumn = status;
            }
        },

        async drop(event, newStatus) {
            event.preventDefault();
            this.hoveringColumn = null;

            const task = this.tasks.find(t => t.id === this.draggingTaskId);
            if (!task || task.status === newStatus) return;

            const oldStatus = task.status;
            task.status = newStatus;

            try {
                const url = updateStatusUrl.replace(':id', task.id);
                await window.axios.patch(url, { status: newStatus });

                this.$dispatch('task-status-updated', { taskId: task.id, status: newStatus });
            } catch (error) {
                console.error('Error actualizando la tarea:', error);
                task.status = oldStatus;
                this.$dispatch('notify', {
                    message: 'No se pudo actualizar el estado de la tarea.',
                    type: 'error',
                });
            }
        },
    }));
};
