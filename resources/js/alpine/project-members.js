import axios from 'axios';

// Instancia privada para peticiones de miembros
const api = axios.create({ headers: { 'Accept': 'application/json' } });

const projectMembersComponent = (projectId = null) => ({
    project: null,
    members: [],
    
    isFetching: false,
    isProcessing: false,
    
    _fetchController: null,

    // Inicializa el componente y observa cambios de proyecto
    init() {
        if (projectId) this.fetchMembers(projectId);

        this.$watch('project', (value) => {
            if (value && value.id) {
                this.fetchMembers(value.id);
            }
        });
    },

    // Obtiene la lista de miembros del servidor
    async fetchMembers(id) {
        if (this._fetchController) this._fetchController.abort();
        this._fetchController = new AbortController();

        this.isFetching = true;
        try {
            const response = await api.get(`/projects/${id}/members`, {
                signal: this._fetchController.signal
            });
            this.members = response.data.data || response.data;
        } catch (error) {
            if (axios.isCancel(error) || error.name === 'CanceledError') return;
            console.error('Error al cargar miembros:', error);
            this.$dispatch('notify', { type: 'error', message: 'No se pudieron cargar los miembros.' });
        } finally {
            this.isFetching = false;
        }
    },

    // Registra un nuevo usuario en el proyecto
    async addMember(userId, role = 'viewer') {
        if (!this.project || this.isProcessing) return;
        
        this.isProcessing = true;
        try {
            const response = await api.post(`/projects/${this.project.id}/members`, {
                user_id: userId,
                role: role
            });
            
            this.$dispatch('notify', { 
                type: 'success', 
                message: response.data.success || 'Usuario añadido correctamente.' 
            });

            this.fetchMembers(this.project.id);
        } catch (error) {
            const message = error.response?.data?.message || error.response?.data?.error || 'No se pudo añadir al usuario';
            this.$dispatch('notify', { type: 'error', message: message });
        } finally {
            this.isProcessing = false;
        }
    },

    // Elimina a un miembro con actualización optimista
    async removeMember(userId) {
        if (!this.project || !confirm('¿Estás seguro de que deseas eliminar a este miembro del proyecto?')) return;

        const previousMembers = [...this.members];
        this.members = this.members.filter(m => m.id !== userId);

        try {
            const response = await api.delete(`/projects/${this.project.id}/members/${userId}`);
            
            this.$dispatch('notify', { 
                type: 'success', 
                message: response.data.success || 'Usuario eliminado.' 
            });

            this.fetchMembers(this.project.id);
        } catch (error) {
            this.members = previousMembers;
            const message = error.response?.data?.message || error.response?.data?.error || 'Error al eliminar usuario';
            this.$dispatch('notify', { type: 'error', message: message });
        }
    }
});

export default () => {
    Alpine.data('projectMembers', projectMembersComponent);
}
