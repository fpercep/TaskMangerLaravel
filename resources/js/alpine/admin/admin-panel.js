export default (users, projects, currentUserId) => ({
    tab: 'users',
    search: '',
    isSearchOpen: false,
    users: users,
    projects: projects,
    currentUserId: currentUserId,

    get filteredUsers() {
        if (!this.search) return this.users;
        
        const term = this.search.toLowerCase();
        return this.users.filter(user => 
            user.name.toLowerCase().includes(term) || 
            user.email.toLowerCase().includes(term)
        );
    },

    get filteredProjects() {
        if (!this.search) return this.projects;
        
        const term = this.search.toLowerCase();
        return this.projects.filter(project => 
            project.name.toLowerCase().includes(term) || 
            (project.description && project.description.toLowerCase().includes(term))
        );
    },

    switchTab(newTab) {
        this.tab = newTab;
        this.search = ''; // Limpiar búsqueda al cambiar de pestaña
    },

    formatDate(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' });
    }
});
