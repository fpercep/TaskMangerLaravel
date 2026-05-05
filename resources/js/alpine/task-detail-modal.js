export default () => {
    Alpine.data('taskDetailModal', (routes) => ({
        show: false,
        task: {},
        taskCache: new Map(), // Caché persistente de datos completos por taskId
        originalDescription: '',
        descOpen: true,
        stepsOpen: true,
        assignOpen: false,
        showNewStepInput: false,
        newStepName: '',
        editingStepId: null,
        editingStepName: '',
        editingTitle: false,
        editingDate: false,
        editingDesc: false,
        originalName: '',
        originalDate: '',

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
            // Fusionar el payload del Kanban (datos shallow) con el caché local (datos completos).
            // El caché tiene prioridad para los campos que el Kanban no mantiene (description, steps).
            const cached = this.taskCache.get(e.detail.id) || {};
            this.task = {
                ...e.detail,
                description: cached.description ?? e.detail.description ?? '',
                // Clonar el array de steps (nunca usar el Proxy reactivo del Kanban directamente)
                steps: cached.steps
                    ? cached.steps.map(s => ({ ...s }))
                    : (e.detail.steps || []).map(s => ({ ...s })),
            };
            // Poblar el caché desde la primera apertura para que siempre esté disponible
            this.taskCache.set(this.task.id, {
                ...cached,
                description: this.task.description,
                steps: this.task.steps.map(s => ({ ...s })),
            });
            this.originalDescription = this.task.description;
            this.originalName = this.task.name;
            this.originalDate = this.task.due_date;
            this.descOpen = true;
            this.stepsOpen = true;
            this.showNewStepInput = false;
            this.newStepName = '';
            this.editingStepId = null;
            this.editingStepName = '';
            this.editingTitle = false;
            this.editingDate = false;
            this.editingDesc = false;
            this.show = true;
        },

        // Persiste los campos completos en el caché local
        updateCache(fields) {
            const current = this.taskCache.get(this.task.id) || {};
            this.taskCache.set(this.task.id, { ...current, ...fields });
        },

        // --- Description, Title, Date ---

        // -- Editing lifecycle helpers --
        startEditingTitle() {
            this.editingTitle = true;
            this.$nextTick(() => this.$refs.titleInput.focus());
        },
        finishEditingTitle() {
            if (!this.editingTitle) return; // Guard: evita doble invocación blur+enter
            this.editingTitle = false;
            this.saveTitle();
        },
        cancelEditingTitle() {
            this.task.name = this.originalName;
            this.editingTitle = false;
        },

        startEditingDate() {
            this.editingDate = true;
            this.$nextTick(() => this.$refs.dateInput.focus());
        },
        finishEditingDate() {
            if (!this.editingDate) return; // Guard: evita doble invocación
            this.editingDate = false;
            this.saveDate();
        },
        cancelEditingDate() {
            this.task.due_date = this.originalDate;
            this.editingDate = false;
        },

        startEditingDesc() {
            if (!this.descOpen) this.descOpen = true;
            this.editingDesc = true;
            this.$nextTick(() => this.$refs.descInput.focus());
        },
        finishEditingDesc() {
            if (!this.editingDesc) return;
            this.editingDesc = false;
            this.saveDescription();
        },
        cancelEditingDesc() {
            this.task.description = this.originalDescription;
            this.editingDesc = false;
        },

        startEditingStep(step) {
            this.editingStepId = step.id;
            this.editingStepName = step.name;
            this.$nextTick(() => this.$refs.stepEditInput?.focus());
        },
        cancelEditingStep() {
            this.editingStepId = null;
        },

        // --- Async saves ---

        async saveDescription() {
            const current = (this.task.description || '').trim();
            if (current === this.originalDescription) return;

            const snapshot = this.originalDescription;
            this.originalDescription = current;
            try {
                const url = routes.update.replace(':id', this.task.id);
                await window.axios.patch(url, { description: current || null });
                this.updateCache({ description: current || '' });
                this.syncKanbanTask({ has_description: !!current });
                this.$dispatch('notify', { message: 'Descripción actualizada.', type: 'success' });
            } catch (error) {
                this.task.description = snapshot;
                this.originalDescription = snapshot;
                console.error('Error actualizando la descripción:', error);
                this.$dispatch('notify', { message: 'No se pudo guardar la descripción.', type: 'error' });
            }
        },

        async saveTitle() {
            const current = (this.task.name || '').trim();
            if (!current) {
                this.task.name = this.originalName;
                return;
            }
            if (current === this.originalName) return;

            const snapshot = this.originalName;
            this.originalName = current;
            try {
                const url = routes.update.replace(':id', this.task.id);
                await window.axios.patch(url, { name: current });
                this.updateCache({ name: current });
                this.syncKanbanTask({ name: current });
                this.$dispatch('notify', { message: 'Título actualizado.', type: 'success' });
            } catch (error) {
                this.task.name = snapshot;
                this.originalName = snapshot;
                console.error('Error actualizando título:', error);
                this.$dispatch('notify', { message: 'No se pudo actualizar el título.', type: 'error' });
            }
        },

        async saveDate() {
            const current = this.task.due_date;
            if (current === this.originalDate) return;

            const snapshot = this.originalDate;
            this.originalDate = current;
            try {
                const url = routes.update.replace(':id', this.task.id);
                await window.axios.patch(url, { due_date: current });
                this.updateCache({ due_date: current });
                this.syncKanbanTask({ due_date: current });
                this.$dispatch('notify', { message: 'Fecha actualizada.', type: 'success' });
            } catch (error) {
                this.task.due_date = snapshot;
                this.originalDate = snapshot;
                console.error('Error actualizando fecha:', error);
                this.$dispatch('notify', { message: 'No se pudo actualizar la fecha.', type: 'error' });
            }
        },

        // --- Fields (status, priority) ---
        selectField(field, value) {
            // Capturar el valor anterior ANTES de la mutación optimista
            const previous = this.task[field];
            this.task[field] = value;
            this.saveField(field, value, previous);
        },

        async saveField(field, value, previous) {
            try {
                const url = routes.update.replace(':id', this.task.id);
                await window.axios.patch(url, { [field]: value });
                this.updateCache({ [field]: value });
                this.syncKanbanTask({ [field]: value });
                this.$dispatch('notify', {
                    message: 'Campo actualizado correctamente.',
                    type: 'success',
                });
            } catch (error) {
                this.task[field] = previous; // Rollback al valor original
                console.error(`Error actualizando ${field}:`, error);
                this.$dispatch('notify', {
                    message: `No se pudo actualizar el campo.`,
                    type: 'error',
                });
            }
        },

        // --- Steps ---
        async addStep() {
            const name = this.newStepName.trim();
            this.showNewStepInput = false;
            this.newStepName = '';
            if (!name) return;

            try {
                const url = routes.storeStep.replace(':id', this.task.id);
                const response = await window.axios.post(url, { name });
                this.task.steps.push(response.data.step);
                this.updateCache({ steps: [...this.task.steps] });
                this.syncKanbanSteps();
            } catch (error) {
                console.error('Error creando paso:', error);
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

            try {
                const url = routes.updateStep.replace(':id', step.id);
                await window.axios.patch(url, { name: current });
                this.updateCache({ steps: [...this.task.steps] });
            } catch (error) {
                step.name = previousName;
                console.error('Error actualizando paso:', error);
                this.$dispatch('notify', { message: 'No se pudo actualizar el nombre del paso.', type: 'error' });
            }
        },

        async toggleStep(step) {
            const previous = step.is_completed;
            step.is_completed = !step.is_completed;

            try {
                const url = routes.toggleStep.replace(':id', step.id);
                await window.axios.patch(url);
                this.updateCache({ steps: [...this.task.steps] });
                this.syncKanbanSteps();
            } catch (error) {
                console.error('Error actualizando paso:', error);
                step.is_completed = previous;
                this.$dispatch('notify', { message: 'No se pudo actualizar el paso.', type: 'error' });
            }
        },

        async deleteStep(step) {
            const index = this.task.steps.findIndex(s => s.id === step.id);
            if (index === -1) return;

            this.task.steps.splice(index, 1);

            try {
                const url = routes.deleteStep.replace(':id', step.id);
                await window.axios.delete(url);
                this.updateCache({ steps: [...this.task.steps] });
                this.syncKanbanSteps();
            } catch (error) {
                console.error('Error eliminando paso:', error);
                this.task.steps.splice(index, 0, step);
                this.updateCache({ steps: [...this.task.steps] });
                this.$dispatch('notify', { message: 'No se pudo eliminar el paso.', type: 'error' });
            }
        },

        // --- Kanban Sync ---
        syncKanbanTask(fields) {
            this.$dispatch('task-modal-updated', { taskId: this.task.id, ...fields });
        },

        syncKanbanSteps() {
            this.$dispatch('task-modal-updated', {
                taskId: this.task.id,
                steps_count: this.task.steps.length,
                completed_steps_count: this.completedStepsCount,
            });
        },
    }));
};
