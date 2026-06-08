import { saveField } from '../task-detail/task-detail-api';
import { PRIORITY_BORDER_ACTIVE, PRIORITY_BORDER_COMPLETED } from '../kanban/kanban-styles';

/**
 * Alpine Component: myDay
 *
 * Gestiona el estado reactivo de la página "Mi Día".
 * - Panel principal: tareasHoy (tareas con due_date = hoy)
 * - Panel sugerencias: tareasMasTarde + tareasAnteriores
 * - Operaciones AJAX: reutiliza saveField de task-detail-api
 * - Estilos de prioridad: reutiliza constantes de kanban-styles
 */
export default (config) => ({
    tareasHoy: config.tareasHoy || [],
    tareasMasTarde: config.tareasMasTarde || [],
    tareasAnteriores: config.tareasAnteriores || [],
    proyectos: config.proyectos || [],
    routes: config.routes || {},
    fechaHoy: config.fechaHoy || '',

    // Filtro de proyecto para sugerencias
    filtroProyecto: null,

    // Estado de procesamiento para evitar doble-clic
    processingIds: new Set(),

    // --- Getters computados ---

    get tareasHoyPendientes() {
        return this.tareasHoy.filter(t => t.status !== 'completed');
    },

    get tareasHoyCompletadas() {
        return this.tareasHoy.filter(t => t.status === 'completed');
    },

    get progreso() {
        const total = this.tareasHoy.length;
        if (total === 0) return { completadas: 0, total: 0, porcentaje: 0 };
        const completadas = this.tareasHoyCompletadas.length;
        return {
            completadas,
            total,
            porcentaje: Math.round((completadas / total) * 100),
        };
    },

    get sugerenciasMasTardeFiltradas() {
        if (!this.filtroProyecto) return this.tareasMasTarde;
        return this.tareasMasTarde.filter(t => t.project_id === this.filtroProyecto);
    },

    get sugerenciasAnterioresFiltradas() {
        if (!this.filtroProyecto) return this.tareasAnteriores;
        return this.tareasAnteriores.filter(t => t.project_id === this.filtroProyecto);
    },

    // --- Estilos de prioridad (reutiliza kanban-styles) ---

    priorityBorderClass(task) {
        if (!task.priority) return 'border-l-gray-300';
        const map = task.status === 'completed'
            ? PRIORITY_BORDER_COMPLETED
            : PRIORITY_BORDER_ACTIVE;
        return map[task.priority] || 'border-l-gray-300';
    },

    priorityDotClass(task) {
        const map = {
            'low': 'bg-priority-low',
            'medium': 'bg-priority-medium',
            'high': 'bg-priority-high',
            'urgent': 'bg-priority-urgent',
        };
        return map[task.priority] || 'bg-gray-300';
    },

    priorityLabel(task) {
        const map = {
            'low': 'Baja',
            'medium': 'Media',
            'high': 'Alta',
            'urgent': 'Urgente',
        };
        return map[task.priority] || '';
    },

    // --- Acciones ---

    /**
     * Añade una tarea a "Mi Día" (asigna due_date = hoy).
     * La mueve de sugerencias al panel principal.
     */
    async addToMyDay(tarea, source) {
        if (this.processingIds.has(tarea.id)) return;
        this.processingIds.add(tarea.id);

        // Optimistic: mover de sugerencias a tareasHoy
        this._removeFromArray(source === 'later' ? this.tareasMasTarde : this.tareasAnteriores, tarea.id);
        const updatedTarea = { ...tarea, due_date: this.fechaHoy, due_date_fmt: 'Hoy', status: tarea.status };
        this.tareasHoy.push(updatedTarea);

        const { ok } = await saveField(this.routes.update, tarea.id, 'due_date', this.fechaHoy);

        if (ok) {
            this.$dispatch('notify', { message: 'Tarea añadida a Mi Día.', type: 'success' });
        } else {
            // Rollback
            this._removeFromArray(this.tareasHoy, tarea.id);
            if (source === 'later') {
                this.tareasMasTarde.push(tarea);
            } else {
                this.tareasAnteriores.push(tarea);
            }
            this.$dispatch('notify', { message: 'No se pudo añadir la tarea.', type: 'error' });
        }

        this.processingIds.delete(tarea.id);
    },

    /**
     * Quita una tarea de "Mi Día" (pone due_date = null).
     * La mueve al panel de sugerencias "Más Tarde".
     */
    async removeFromMyDay(tarea) {
        if (this.processingIds.has(tarea.id)) return;
        this.processingIds.add(tarea.id);

        // Optimistic: mover a sugerencias
        this._removeFromArray(this.tareasHoy, tarea.id);
        const restoredTarea = { ...tarea, due_date: null, due_date_fmt: 'Sin fecha' };
        this.tareasMasTarde.unshift(restoredTarea);

        const { ok } = await saveField(this.routes.update, tarea.id, 'due_date', null);

        if (ok) {
            this.$dispatch('notify', { message: 'Tarea quitada de Mi Día.', type: 'success' });
        } else {
            // Rollback
            this._removeFromArray(this.tareasMasTarde, tarea.id);
            this.tareasHoy.push(tarea);
            this.$dispatch('notify', { message: 'No se pudo quitar la tarea.', type: 'error' });
        }

        this.processingIds.delete(tarea.id);
    },

    /**
     * Toggle completar/descompletar tarea.
     * Reutiliza saveField de task-detail-api.
     */
    async toggleComplete(tarea) {
        if (this.processingIds.has(tarea.id)) return;
        this.processingIds.add(tarea.id);

        const prevStatus = tarea.status;
        const newStatus = tarea.status === 'completed' ? 'in_progress' : 'completed';

        // Optimistic update
        tarea.status = newStatus;

        const { ok } = await saveField(this.routes.update, tarea.id, 'status', newStatus);

        if (!ok) {
            tarea.status = prevStatus;
            this.$dispatch('notify', { message: 'No se pudo actualizar el estado.', type: 'error' });
        }

        this.processingIds.delete(tarea.id);
    },

    /**
     * Establece el filtro de proyecto para sugerencias.
     */
    setFiltroProyecto(projectId) {
        this.filtroProyecto = projectId;
    },

    handleRealtimeUpdate(updatedTask) {
        const isAssignedToMe = updatedTask.assigned_user_id === window.AppUserId;

        // Eliminarla de todos los arrays primero
        this._removeFromArray(this.tareasHoy, updatedTask.id);
        this._removeFromArray(this.tareasMasTarde, updatedTask.id);
        this._removeFromArray(this.tareasAnteriores, updatedTask.id);

        if (!isAssignedToMe) {
            return;
        }

        let formattedDate = 'Sin fecha';
        if (updatedTask.due_date) {
            const parts = updatedTask.due_date.split('-');
            if (parts.length === 3) {
                formattedDate = `${parts[2]}/${parts[1]}/${parts[0]}`;
            }
        }

        const mappedTask = {
            id: updatedTask.id,
            name: updatedTask.name,
            status: updatedTask.status,
            priority: updatedTask.priority,
            due_date: updatedTask.due_date,
            due_date_fmt: formattedDate,
            project_id: updatedTask.project_id,
            project_name: updatedTask.project ? updatedTask.project.name : 'Sin Proyecto',
        };

        if (updatedTask.due_date) {
            if (updatedTask.due_date === this.fechaHoy) {
                this.tareasHoy.push(mappedTask);
            } else if (updatedTask.due_date < this.fechaHoy) {
                if (updatedTask.status !== 'completed' && updatedTask.status !== 'cancelled') {
                    this.tareasAnteriores.push(mappedTask);
                }
            } else {
                if (updatedTask.status !== 'completed' && updatedTask.status !== 'cancelled') {
                    this.tareasMasTarde.push(mappedTask);
                }
            }
        } else {
            if (updatedTask.status !== 'completed' && updatedTask.status !== 'cancelled') {
                this.tareasMasTarde.push(mappedTask);
            }
        }
    },

    handleRealtimeDelete(taskId) {
        this._removeFromArray(this.tareasHoy, taskId);
        this._removeFromArray(this.tareasMasTarde, taskId);
        this._removeFromArray(this.tareasAnteriores, taskId);
    },

    // --- Helpers internos ---

    _removeFromArray(arr, id) {
        const index = arr.findIndex(t => t.id === id);
        if (index !== -1) arr.splice(index, 1);
    },
});
