import axios from 'axios';

// Instancia configurada para la API
const api = axios.create({ headers: { 'Accept': 'application/json' } });

/**
 * STORE: membersStore
 * Encapsula el estado global y la lógica de datos.
 * Implementa Actualización Optimista con Rollback.
 */
const membersStore = {
    members: [],
    isFetching: false,
    isProcessing: false,
    _fetchController: null,

    // Carga inicial o refresco
    async fetch(projectId) {
        if (!projectId) return;
        if (this._fetchController) this._fetchController.abort();
        this._fetchController = new AbortController();

        this.isFetching = true;
        try {
            const { data } = await api.get(`/projects/${projectId}/members`, {
                signal: this._fetchController.signal
            });
            this.members = data.data;
        } catch (error) {
            if (!axios.isCancel(error)) console.error('Error fetchMembers:', error);
        } finally {
            this.isFetching = false;
            this._fetchController = null;
        }
    },

    // Añadir miembro individual (Mapea a ProjectMemberController@store)
    async add(projectId, userId, role = 'viewer') {
        return this._execute(async () => {
            const { data } = await api.post(`/projects/${projectId}/members`, {
                user_id: userId,
                role: role
            });
            await this._fetchRaw(projectId);
            return data.success || 'Usuario añadido correctamente.';
        });
    },

    // Fetch interno sin manejo de errores — para uso dentro de _execute
    async _fetchRaw(projectId) {
        const { data } = await api.get(`/projects/${projectId}/members`);
        this.members = data.data;
    },

    // Actualización de Rol (Optimista)
    async updateRole(projectId, userId, role) {
        const previousState = JSON.parse(JSON.stringify(this.members));

        const member = this.members.find(m => m.id === userId);
        if (member) member.role = role;

        return this._execute(async () => {
            await api.patch(`/projects/${projectId}/members/${userId}`, { role });
            return 'Permisos actualizados correctamente.';
        }, previousState);
    },

    // Eliminación Individual (Optimista)
    async remove(projectId, userId) {
        const previousState = JSON.parse(JSON.stringify(this.members));

        this.members = this.members.filter(m => m.id !== userId);

        return this._execute(async () => {
            await api.delete(`/projects/${projectId}/members/${userId}`);
            return 'Miembro eliminado del proyecto.';
        }, previousState);
    },

    // Eliminación Masiva (Optimista)
    async removeBulk(projectId, userIds) {
        const previousState = JSON.parse(JSON.stringify(this.members));

        this.members = this.members.filter(m => !userIds.includes(m.id));

        return this._execute(async () => {
            await api.delete(`/projects/${projectId}/members`, {
                data: { user_ids: userIds }
            });
            return `${userIds.length} miembros eliminados.`;
        }, previousState);
    },

    // Sincronización / Adición Masiva
    async sync(projectId, users) {
        return this._execute(async () => {
            const { data } = await api.post(`/projects/${projectId}/members/sync`, { users });
            await this._fetchRaw(projectId);
            return data.success || 'Usuarios añadidos.';
        });
    },

    /**
     * Helper de ejecución centralizado
     * Maneja: isProcessing, Notificaciones y Rollbacks
     */
    async _execute(callback, rollbackState = null) {
        this.isProcessing = true;
        try {
            const message = await callback();
            window.dispatchEvent(new CustomEvent('notify', {
                detail: { type: 'success', message }
            }));
            return true;
        } catch (error) {
            if (rollbackState) this.members = rollbackState;

            const msg = error.response?.data?.message
                || error.response?.data?.error
                || 'Error en la operación';

            window.dispatchEvent(new CustomEvent('notify', {
                detail: { type: 'error', message: msg }
            }));
            return false;
        } finally {
            this.isProcessing = false;
        }
    }
};

/**
 * COMPONENTE: projectMembers
 * Actúa como puente entre el template HTML y el Store.
 * Aquí reside la lógica de interacción (confirmaciones).
 */
const projectMembersComponent = (initialProjectId = null) => ({
    projectId: initialProjectId,

    get store()   { return Alpine.store('members'); },
    get members() { return this.store.members; },
    get isBusy()  { return this.store.isFetching || this.store.isProcessing; },

    init() {
        if (this.projectId) this.store.fetch(this.projectId);
        this.$watch('projectId', (id) => id && this.store.fetch(id));
    },

    changeRole(userId, newRole) {
        this.store.updateRole(this.projectId, userId, newRole);
    },

    addMember(userId, role = 'viewer') {
        this.store.add(this.projectId, userId, role);
    },

    removeMember(userId) {
        if (confirm('¿Estás seguro de que deseas eliminar a este miembro del proyecto?')) {
            this.store.remove(this.projectId, userId);
        }
    },

    removeMembers(userIds) {
        if (confirm(`¿Eliminar a los ${userIds.length} miembros seleccionados?`)) {
            this.store.removeBulk(this.projectId, userIds);
        }
    },

    syncMembers(users) {
        this.store.sync(this.projectId, users);
    }
});

/**
 * Registro del Plugin
 */
export default function registerMembersPlugin() {
    Alpine.store('members', membersStore);
    Alpine.data('projectMembers', projectMembersComponent);
}