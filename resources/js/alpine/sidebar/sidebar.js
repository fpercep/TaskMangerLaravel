/**
 * Componente Alpine: sidebar
 * Gestiona el estado del sidebar y los handlers de eventos Reverb.
 */
export default () => ({
    collapsed: Alpine.$persist(false).as('sidebar_collapsed'),

    toggleSidebar() {
        this.collapsed = !this.collapsed;
    },

    // ═══════════════════════════════════════════
    //  Handlers de eventos Reverb
    // ═══════════════════════════════════════════

    handleProjectRemoved(detail) {
        const { project_id } = detail;

        // 1. Eliminar el proyecto del sidebar DOM
        const projectLink = document.querySelector(`a[href$="/projects/${project_id}"]`);
        if (projectLink) {
            const projectItem = projectLink.closest('.group.relative');
            if (projectItem) {
                projectItem.remove();
            }
        }

        // 2. Si el usuario está viendo ese proyecto, redirigir al dashboard
        if (window.location.pathname.endsWith(`/projects/${project_id}`)) {
            window.location.href = '/';
        }
    },

    handleProjectAdded(detail) {
        const { project_id, project_name } = detail;

        const projectList = this.$refs.projectList;
        if (!projectList) return;

        // Evitar duplicados
        if (document.querySelector(`a[href$="/projects/${project_id}"]`)) return;

        // Asignar color rotativo basado en los proyectos existentes
        const colors = ['bg-emerald-400', 'bg-indigo-400', 'bg-orange-400', 'bg-rose-400', 'bg-sky-400'];
        const color = colors[projectList.children.length % colors.length];

        const item = document.createElement('div');
        item.className = 'group relative flex items-center justify-between px-2 py-1.5 text-sidebar-item font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-800 rounded-md transition-colors';
        item.innerHTML = `
            <a href="/projects/${project_id}" class="flex items-center truncate pl-2 flex-1 outline-none">
                <span class="w-2 h-2 rounded-full ${color} mr-2.5 shrink-0 mix-blend-multiply"></span>
                <span class="truncate">${project_name}</span>
            </a>
        `;

        projectList.appendChild(item);
    },

    handleProjectUpdated(detail) {
        const { project_id, project_name } = detail;

        // Actualizar el nombre en el sidebar
        const projectLink = document.querySelector(`a[href$="/projects/${project_id}"]`);
        if (projectLink) {
            const nameSpan = projectLink.querySelector('span.truncate');
            if (nameSpan) nameSpan.textContent = project_name;
        }

        // Si estamos viendo ese proyecto, actualizar el título de la página
        if (window.location.pathname.endsWith(`/projects/${project_id}`)) {
            const h1 = document.querySelector('h1');
            if (h1) h1.textContent = project_name;
        }
    },

    // ═══════════════════════════════════════════
    //  Acciones de UI (modals)
    // ═══════════════════════════════════════════

    editProject(id, name, description) {
        this.$dispatch('open-modal', {
            name: 'edit-project',
            payload: { project: { id, name, description } }
        });
    },

    deleteProject(id, name) {
        this.$dispatch('open-modal', {
            name: 'delete-project',
            payload: { project: { id, name } }
        });
    }
});
