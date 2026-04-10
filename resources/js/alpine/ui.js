export default () => {
    Alpine.data('layoutPanel', () => ({
        showSuggestions: Alpine.$persist(true).as('suggestions_panel_visible'),
        
        toggleSuggestions() {
            this.showSuggestions = !this.showSuggestions;
            // Esperar a que Alpine actualice el DOM para recrear iconos si es necesario
            this.$nextTick(() => {
                if (window.lucide) {
                    window.lucide.createIcons();
                }
            });
        }
    }));

    Alpine.data('sidebar', () => ({
        collapsed: Alpine.$persist(false).as('sidebar_collapsed'),
        
        toggleSidebar() {
            this.collapsed = !this.collapsed;
            this.$nextTick(() => {
                if (window.lucide) {
                    window.lucide.createIcons();
                }
            });
        }
    }));

    Alpine.data('accordion', (initialState = false) => ({
        open: initialState,
        
        toggle() {
            this.open = !this.open;
        }
    }));
};
