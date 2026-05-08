export default function projectMembers() {
    return {
        projectId: null,
        activeFilter: 'Todos',
        selected: [],
        currentUserId: null,

        init() {
            this.$watch('projectId', id => {
                if (id) Alpine.store('members').fetch(id);
            });
            
            this.$watch('activeFilter', () => this.selected = []);
        },

        isProtected(member) {
            return member.id === this.currentUserId || member.role === 'admin';
        },

        get members() {
            return Alpine.store('members').members;
        },

        get filteredMembers() {
            if (this.activeFilter === 'Todos') return this.members;
            return this.members.filter(m => (m.role || '').toLowerCase() === this.activeFilter.toLowerCase());
        },

        get isBusy() {
            return Alpine.store('members').isFetching;
        },

        async handleUserSelected(userId) {
            await Alpine.store('members').add(this.projectId, userId, 'editor');
        },

        async updateRole(userId, role) {
            await Alpine.store('members').updateRole(this.projectId, userId, role);
        },

        async removeMember(userId) {
            if (confirm('¿Estás seguro de que deseas eliminar a este miembro del proyecto?')) {
                await Alpine.store('members').remove(this.projectId, userId);
            }
        },

        toggleAll(checked) {
            this.selected = checked ? this.filteredMembers.map(m => m.id) : [];
        }
    };
}
