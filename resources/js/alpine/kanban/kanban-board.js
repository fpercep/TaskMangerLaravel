import { PRIORITY_BORDER_ACTIVE, PRIORITY_BORDER_COMPLETED } from './kanban-styles';

/**
 * Alpine Component: kanbanBoard
 *
 * Componente de UI del tablero Kanban.
 * Gestiona el estado visual (drag & drop, rename inline),
 * los getters derivados, y delega las operaciones al store 'kanban'.
 */
export default () => ({
    draggingTaskId: null,
    renamingTaskId: null,
    hoveringColumn: null,

    get store() {
        return Alpine.store('kanban');
    },

    get today() {
        const d = new Date();
        d.setHours(0, 0, 0, 0);
        return d;
    },

    get tasksGrouped() {
        return this.store.tasks.reduce((groups, task) => {
            if (!groups[task.status]) groups[task.status] = [];
            groups[task.status].push(task);
            return groups;
        }, { 'pending': [], 'in_progress': [], 'completed': [] });
    },

    // --- Presentación ---

    isOverdue(task) {
        if (!task.due_date || task.status === 'completed') return false;
        return new Date(task.due_date) < this.today;
    },

    priorityBorderClass(task) {
        if (!task.priority) return 'border-l-gray-300';
        const map = task.status === 'completed'
            ? PRIORITY_BORDER_COMPLETED
            : PRIORITY_BORDER_ACTIVE;
        return map[task.priority] || 'border-l-gray-300';
    },

    // --- Drag & Drop ---

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

        const task = this.store.taskById(this.draggingTaskId);
        if (!task || task.status === newStatus) return;

        const ok = await this.store.updateTask(task.id, { status: newStatus });

        if (ok) {
            this.$dispatch('task-status-updated', { taskId: task.id, status: newStatus });
        }

        this.$dispatch('notify', {
            message: ok
                ? 'Estado actualizado correctamente.'
                : 'No se pudo actualizar el estado de la tarea.',
            type: ok ? 'success' : 'error',
        });
    },

    // --- Rename inline ---

    renameTask(task) {
        this.renamingTaskId = task.id;
    },

    cancelRenaming() {
        this.renamingTaskId = null;
    },

    async updateTaskName(task, newName) {
        if (!newName || newName.trim() === '' || newName.trim() === task.name.trim()) {
            this.cancelRenaming();
            return;
        }

        this.renamingTaskId = null;
        const ok = await this.store.updateTask(task.id, { name: newName.trim() });

        this.$dispatch('notify', {
            message: ok
                ? 'Nombre actualizado correctamente.'
                : 'No se pudo actualizar el nombre de la tarea.',
            type: ok ? 'success' : 'error',
        });
    },

    // --- Acciones delegadas al store ---

    async duplicateTask(task) {
        const result = await this.store.duplicateTask(task.id);

        this.$dispatch('notify', {
            message: result?.message || (result ? 'Tarea duplicada correctamente.' : 'No se pudo duplicar la tarea.'),
            type: result ? 'success' : 'error',
        });
    },

    async deleteTask(task) {
        const result = await this.store.deleteTask(task.id);

        this.$dispatch('notify', {
            message: result?.message || (result ? 'Tarea eliminada correctamente.' : 'No se pudo eliminar la tarea.'),
            type: result ? 'success' : 'error',
        });
    },
});
