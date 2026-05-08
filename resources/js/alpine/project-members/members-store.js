import axios from 'axios';

const api = axios.create({ headers: { 'Accept': 'application/json' } });

export default function membersStore() {
    return {
        members: [],
        isFetching: false,
        isProcessing: false,
        _fetchController: null,

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
                if (!axios.isCancel(error)) console.error('Error fetching members:', error);
            } finally {
                this.isFetching = false;
            }
        },

        async add(projectId, userId, role = 'editor') {
            try {
                const { data } = await api.post(`/projects/${projectId}/members`, { user_id: userId, role });
                await this.fetch(projectId);
                return true;
            } catch (error) {
                console.error('Error adding member:', error);
                return false;
            }
        },

        async updateRole(projectId, userId, role) {
            const previousState = [...this.members];
            const member = this.members.find(m => m.id === userId);
            if (member) member.role = role;

            try {
                await api.patch(`/projects/${projectId}/members/${userId}`, { role });
            } catch (error) {
                this.members = previousState;
                console.error('Error updating role:', error);
            }
        },

        async remove(projectId, userId) {
            const previousState = [...this.members];
            this.members = this.members.filter(m => m.id !== userId);

            try {
                await api.delete(`/projects/${projectId}/members/${userId}`);
            } catch (error) {
                this.members = previousState;
                console.error('Error removing member:', error);
            }
        }
    };
}
