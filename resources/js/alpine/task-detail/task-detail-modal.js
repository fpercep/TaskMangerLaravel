import * as api from './task-detail-api';

/**
 * Alpine Component: taskDetailModal
 *
 * UI del modal de detalle de tarea.
 * Gestiona el estado visual (flags de edición, secciones colapsables),
 * los getters derivados (sortedSteps, completedStepsCount),
 * y delega las operaciones async a task-detail-api.js.
 *
 * Se registra en app.js como: Alpine.data('taskDetailModal', taskDetailModal)
 */
export default (routes) => ({
    show: false,
    task: {},
    taskCache: new Map(),

    // --- Estado de edición ---
    editingTitle: false,
    editingDate: false,
    editingDesc: false,
    editingStepId: null,
    editingStepName: '',

    // --- Snapshots para rollback ---
    originalName: '',
    originalDate: '',
    originalDescription: '',

    // --- UI toggles ---
    descOpen: true,
    stepsOpen: true,
    assignOpen: false,
    showNewStepInput: false,
    newStepName: '',

    // --- Computed ---
    get sortedSteps() {
        return [...(this.task.steps || [])].sort((a, b) => {
            if (a.is_completed !== b.is_completed) return a.is_completed ? 1 : -1;
            return 0;
        });
    },

    get completedStepsCount() {
        return (this.task.steps || []).filter(s => s.is_completed).length;
    },

    // --- Lifecycle ---
    handleOpen(e) {
        const cached = this.taskCache.get(e.detail.id) || {};
        this.task = {
            ...e.detail,
            description: cached.description ?? e.detail.description ?? '',
            steps: cached.steps
                ? cached.steps.map(s => ({ ...s }))
                : (e.detail.steps || []).map(s => ({ ...s })),
        };

        this.taskCache.set(this.task.id, {
            ...cached,
            description: this.task.description,
            steps: this.task.steps.map(s => ({ ...s })),
        });

        this.originalDescription = this.task.description;
        this.originalName = this.task.name;
        this.originalDate = this.task.due_date;

        // Reset UI state
        this.descOpen = true;
        this.stepsOpen = true;
        this.assignOpen = false;
        this.showNewStepInput = false;
        this.newStepName = '';
        this.editingStepId = null;
        this.editingStepName = '';
        this.editingTitle = false;
        this.editingDate = false;
        this.editingDesc = false;
        this.show = true;
    },

    // --- Cache helper ---
    updateCache(fields) {
        const current = this.taskCache.get(this.task.id) || {};
        this.taskCache.set(this.task.id, { ...current, ...fields });
    },

    // ======================
    //  Inline Editing — DRY
    // ======================

    startEditing(field, ref) {
        this[field] = true;
        this.$nextTick(() => this.$refs[ref]?.focus());
    },

    cancelEditing(field, valueKey, snapshotKey) {
        this.task[valueKey] = this[snapshotKey];
        this[field] = false;
    },

    // -- Title --
    startEditingTitle()  { this.startEditing('editingTitle', 'titleInput'); },
    cancelEditingTitle() { this.cancelEditing('editingTitle', 'name', 'originalName'); },
    finishEditingTitle() {
        if (!this.editingTitle) return;
        this.editingTitle = false;
        this.saveTitle();
    },

    // -- Date --
    startEditingDate()  { this.startEditing('editingDate', 'dateInput'); },
    cancelEditingDate() { this.cancelEditing('editingDate', 'due_date', 'originalDate'); },
    finishEditingDate() {
        if (!this.editingDate) return;
        this.editingDate = false;
        this.saveDate();
    },

    // -- Description --
    startEditingDesc() {
        if (!this.descOpen) this.descOpen = true;
        this.startEditing('editingDesc', 'descInput');
    },
    cancelEditingDesc() { this.cancelEditing('editingDesc', 'description', 'originalDescription'); },
    finishEditingDesc() {
        if (!this.editingDesc) return;
        this.editingDesc = false;
        this.saveDescription();
    },

    // -- Step name --
    startEditingStep(step) {
        this.editingStepId = step.id;
        this.editingStepName = step.name;
        this.$nextTick(() => this.$refs.stepEditInput?.focus());
    },
    cancelEditingStep() { this.editingStepId = null; },

    // =================
    //  Async Saves
    // =================

    async saveTitle() {
        const current = (this.task.name || '').trim();
        if (!current) { this.task.name = this.originalName; return; }
        if (current === this.originalName) return;

        const snapshot = this.originalName;
        this.originalName = current;

        const { ok } = await api.saveField(routes.update, this.task.id, 'name', current);
        if (ok) {
            this.updateCache({ name: current });
            this.syncKanbanTask({ name: current });
            this.$dispatch('notify', { message: 'Título actualizado.', type: 'success' });
        } else {
            this.task.name = snapshot;
            this.originalName = snapshot;
            this.$dispatch('notify', { message: 'No se pudo actualizar el título.', type: 'error' });
        }
    },

    async saveDate() {
        const current = this.task.due_date;
        if (current === this.originalDate) return;

        const snapshot = this.originalDate;
        this.originalDate = current;

        const { ok } = await api.saveField(routes.update, this.task.id, 'due_date', current);
        if (ok) {
            this.updateCache({ due_date: current });
            this.syncKanbanTask({ due_date: current });
            this.$dispatch('notify', { message: 'Fecha actualizada.', type: 'success' });
        } else {
            this.task.due_date = snapshot;
            this.originalDate = snapshot;
            this.$dispatch('notify', { message: 'No se pudo actualizar la fecha.', type: 'error' });
        }
    },

    async saveDescription() {
        const current = (this.task.description || '').trim();
        if (current === this.originalDescription) return;

        const snapshot = this.originalDescription;
        this.originalDescription = current;

        const { ok } = await api.saveField(routes.update, this.task.id, 'description', current || null);
        if (ok) {
            this.updateCache({ description: current || '' });
            this.syncKanbanTask({ has_description: !!current });
            this.$dispatch('notify', { message: 'Descripción actualizada.', type: 'success' });
        } else {
            this.task.description = snapshot;
            this.originalDescription = snapshot;
            this.$dispatch('notify', { message: 'No se pudo guardar la descripción.', type: 'error' });
        }
    },

    // --- Field selects (status, priority) ---
    async selectField(field, value) {
        const previous = this.task[field];
        this.task[field] = value;

        const { ok } = await api.saveField(routes.update, this.task.id, field, value);
        if (ok) {
            this.updateCache({ [field]: value });
            this.syncKanbanTask({ [field]: value });
            this.$dispatch('notify', { message: 'Campo actualizado correctamente.', type: 'success' });
        } else {
            this.task[field] = previous;
            this.$dispatch('notify', { message: 'No se pudo actualizar el campo.', type: 'error' });
        }
    },

    // =================
    //  Steps CRUD
    // =================

    async addStep() {
        const name = this.newStepName.trim();
        this.showNewStepInput = false;
        this.newStepName = '';
        if (!name) return;

        const { ok, step } = await api.createStep(routes.storeStep, this.task.id, name);
        if (ok && step) {
            this.task.steps.push(step);
            this.updateCache({ steps: [...this.task.steps] });
            this.syncKanbanSteps();
        } else {
            this.$dispatch('notify', { message: 'No se pudo crear el paso.', type: 'error' });
        }
    },

    async saveStepName(step) {
        const current = (this.editingStepName || '').trim();
        if (current === step.name || !current) {
            this.editingStepId = null;
            return;
        }

        const previousName = step.name;
        step.name = current;
        this.editingStepId = null;

        const { ok } = await api.updateStepName(routes.updateStep, step.id, current);
        if (ok) {
            this.updateCache({ steps: [...this.task.steps] });
        } else {
            step.name = previousName;
            this.$dispatch('notify', { message: 'No se pudo actualizar el nombre del paso.', type: 'error' });
        }
    },

    async toggleStep(step) {
        const previous = step.is_completed;
        step.is_completed = !step.is_completed;

        const { ok } = await api.toggleStepCompleted(routes.toggleStep, step.id);
        if (ok) {
            this.updateCache({ steps: [...this.task.steps] });
            this.syncKanbanSteps();
        } else {
            step.is_completed = previous;
            this.$dispatch('notify', { message: 'No se pudo actualizar el paso.', type: 'error' });
        }
    },

    async deleteStep(step) {
        const index = this.task.steps.findIndex(s => s.id === step.id);
        if (index === -1) return;

        this.task.steps.splice(index, 1);

        const { ok } = await api.destroyStep(routes.deleteStep, step.id);
        if (ok) {
            this.updateCache({ steps: [...this.task.steps] });
            this.syncKanbanSteps();
        } else {
            this.task.steps.splice(index, 0, step);
            this.updateCache({ steps: [...this.task.steps] });
            this.$dispatch('notify', { message: 'No se pudo eliminar el paso.', type: 'error' });
        }
    },

    // ======================
    //  Kanban Sync
    // ======================

    syncKanbanTask(fields) {
        Alpine.store('kanban').syncFromModal({ taskId: this.task.id, ...fields });
    },

    syncKanbanSteps() {
        Alpine.store('kanban').syncFromModal({
            taskId: this.task.id,
            steps_count: this.task.steps.length,
            completed_steps_count: this.completedStepsCount,
        });
    },

    // ======================
    //  Realtime Sync (WebSockets)
    // ======================

    handleRealtimeUpdate(updatedTask) {
        // Solo aplicar si es la tarea que está actualmente abierta
        if (!this.show || this.task.id !== updatedTask.id) return;

        // Mezclar los campos actualizados
        this.task = {
            ...this.task,
            name: updatedTask.name,
            description: updatedTask.description ?? this.task.description,
            status: updatedTask.status,
            priority: updatedTask.priority,
            due_date: updatedTask.due_date,
        };

        // Si el payload incluye steps, actualizar el array de steps también
        if (updatedTask.steps) {
            this.task.steps = updatedTask.steps.map(s => ({ ...s }));
        }

        // Actualizar snapshots si NO estamos editando, para evitar sobrescribir lo que el usuario está tecleando
        if (!this.editingTitle) this.originalName = this.task.name;
        if (!this.editingDesc) this.originalDescription = this.task.description;
        if (!this.editingDate) this.originalDate = this.task.due_date;

        // Actualizar el caché para que persista al reabrir
        this.updateCache({ 
            ...this.task,
            steps: this.task.steps.map(s => ({ ...s }))
        });
    },

    handleRealtimeDelete(taskId) {
        if (this.show && this.task.id === taskId) {
            this.show = false;
            this.$dispatch('notify', { message: 'Esta tarea ha sido eliminada por otro miembro del proyecto.', type: 'warning' });
        }
    },
});
