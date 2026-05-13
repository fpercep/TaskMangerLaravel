/**
 * Alpine Store: kanban
 *
 * Estado compartido del tablero Kanban.
 * Encapsula el array de tareas, las operaciones CRUD contra la API,
 * y la sincronización entrante desde el modal de detalle.
 *
 * Se registra vacío en app.js y se hidrata desde Blade vía init().
 */
export default function kanbanStore() {
    return {
        tasks: [],
        projectId: null,
        routes: {},
        processingTaskIds: new Set(),

        // --- Inicialización ---
        init(projectId, tasks, routes) {
            this.projectId = projectId;
            this.tasks = tasks;
            this.routes = routes;
        },

        // --- Lookups ---
        taskById(id) {
            return this.tasks.find(t => t.id === id);
        },

        // --- Operaciones API ---

        async updateTask(taskId, payload) {
            const task = this.taskById(taskId);
            if (!task) return false;

            const snapshot = {};
            for (const key of Object.keys(payload)) {
                snapshot[key] = task[key];
            }
            Object.assign(task, payload);

            try {
                const url = this.routes.update.replace(':id', taskId);
                await window.axios.patch(url, payload);
                return true;
            } catch (error) {
                Object.assign(task, snapshot);
                console.error('Error actualizando tarea:', error);
                return false;
            }
        },

        async duplicateTask(taskId) {
            if (this.processingTaskIds.has(taskId)) return null;
            this.processingTaskIds.add(taskId);

            try {
                const url = this.routes.duplicate.replace(':id', taskId);
                const { data } = await window.axios.post(url);

                if (data?.task) {
                    this.tasks.push(data.task);
                    return data;
                }
                return null;
            } catch (error) {
                console.error('Error duplicando tarea:', error);
                return null;
            } finally {
                this.processingTaskIds.delete(taskId);
            }
        },

        async deleteTask(taskId) {
            if (this.processingTaskIds.has(taskId)) return null;
            this.processingTaskIds.add(taskId);

            const snapshot = [...this.tasks];
            this.tasks = this.tasks.filter(t => t.id !== taskId);

            try {
                const { data } = await window.axios.delete(
                    this.routes.delete.replace(':id', taskId)
                );
                return data;
            } catch (error) {
                this.tasks = snapshot;
                console.error('Error eliminando tarea:', error);
                return null;
            } finally {
                this.processingTaskIds.delete(taskId);
            }
        },

        // --- Sincronización desde el modal de detalle ---
        syncFromModal(detail) {
            const ALLOWED_FIELDS = [
                'name', 'status', 'priority', 'due_date',
                'description', 'has_description',
                'steps_count', 'completed_steps_count',
                'assigned_user_id', 'assigned_user',
            ];

            const task = this.taskById(detail.taskId);
            if (!task) return;

            for (const key of ALLOWED_FIELDS) {
                if (key in detail) {
                    task[key] = detail[key];
                }
            }
        },

        // --- Sincronización desde WebSockets (Reverb) ---
        upsertTask(taskData) {
            // Solo procesar si pertenece a este proyecto
            if (taskData.project_id !== this.projectId) return;

            const index = this.tasks.findIndex(t => t.id === taskData.id);
            if (index !== -1) {
                // Actualizar existente
                Object.assign(this.tasks[index], taskData);
            } else {
                // Añadir nueva
                this.tasks.push(taskData);
            }
        },

        removeTask(taskId) {
            this.tasks = this.tasks.filter(t => t.id !== taskId);
        },
    };
}
