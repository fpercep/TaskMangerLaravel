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
    Alpine.data('kanbanBoard', (initialTasks, routes) => ({
        tasks: initialTasks,
        draggingTaskId: null,
        renamingTaskId: null,
        hoveringColumn: null,

        get tasksGrouped() {
            return this.tasks.reduce((groups, task) => {
                if (!groups[task.status]) groups[task.status] = [];
                groups[task.status].push(task);
                return groups;
            }, { 'pending': [], 'in_progress': [], 'completed': [] });
        },

        renameTask(task) {
            this.renamingTaskId = task.id;
        },

        cancelRenaming() {
            this.renamingTaskId = null;
        },

        async updateTaskName(task, newName) {
            if (!newName || newName.trim() === '' || newName === task.name) {
                this.cancelRenaming();
                return;
            }

            const oldName = task.name;
            task.name = newName;
            this.renamingTaskId = null;

            try {
                const url = routes.update.replace(':id', task.id);
                await window.axios.patch(url, { name: newName });
                
                this.$dispatch('notify', {
                    message: 'Nombre actualizado correctamente.',
                    type: 'success',
                });
            } catch (error) {
                console.error('Error actualizando el nombre:', error);
                task.name = oldName;
                this.$dispatch('notify', {
                    message: 'No se pudo actualizar el nombre de la tarea.',
                    type: 'error',
                });
            }
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
                const url = routes.update.replace(':id', task.id);
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

        async duplicateTask(task) {
            try {
                const url = routes.duplicate.replace(':id', task.id);
                const response = await window.axios.post(url);
                
                if (response.data && response.data.task) {
                    this.tasks.push(response.data.task);
                    this.$dispatch('notify', {
                        message: response.data.message || 'Tarea duplicada correctamente.',
                        type: 'success',
                    });
                }
            } catch (error) {
                console.error('Error duplicando la tarea:', error);
                this.$dispatch('notify', {
                    message: 'No se pudo duplicar la tarea.',
                    type: 'error',
                });
            }
        },

        async deleteTask(task) {
            try {
                const url = routes.delete.replace(':id', task.id);
                const response = await window.axios.delete(url);
                
                this.tasks = this.tasks.filter(t => t.id !== task.id);
                this.$dispatch('notify', {
                    message: response.data.message || 'Tarea eliminada correctamente.',
                    type: 'success',
                });
            } catch (error) {
                console.error('Error eliminando la tarea:', error);
                this.$dispatch('notify', {
                    message: 'No se pudo eliminar la tarea.',
                    type: 'error',
                });
            }
        },
    }));
};
