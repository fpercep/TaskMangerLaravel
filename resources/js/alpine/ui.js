export default () => {
    Alpine.data('layoutPanel', () => ({
        showSuggestions: Alpine.$persist(true).as('suggestions_panel_visible'),
        
        toggleSuggestions() {
            this.showSuggestions = !this.showSuggestions;
        }
    }));

    Alpine.data('sidebar', () => ({
        collapsed: Alpine.$persist(false).as('sidebar_collapsed'),
        
        toggleSidebar() {
            this.collapsed = !this.collapsed;
        },

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
    }));

    Alpine.data('accordion', (initialState = false) => ({
        open: initialState,
        
        toggle() {
            this.open = !this.open;
        }
    }));

    Alpine.data('modalState', (modalName, extraData = {}) => ({
        show: false,
        ...extraData,
        handleOpen(event) {
            if (event.detail.name === modalName) {
                this.show = true;
                if(event.detail.payload) {
                    Object.assign(this, event.detail.payload);
                }
                if (this.onOpen) this.onOpen();
            }
        },
        handleClose(event) {
            if (!event || event.detail.name === modalName) {
                this.show = false;
            }
        }
    }));

    Alpine.data('prioritySlider', (initialIndex = 1) => ({
        priorityIndex: initialIndex,
        priorities: [
            { name: 'low', label: 'Baja', color: 'bg-gray-200', borderColor: 'border-gray-300', textColor: 'text-gray-500' },
            { name: 'medium', label: 'Media', color: 'bg-orange-400', borderColor: 'border-orange-500', textColor: 'text-orange-600' },
            { name: 'high', label: 'Alta', color: 'bg-orange-600', borderColor: 'border-orange-700', textColor: 'text-orange-700' },
            { name: 'urgent', label: 'Urgente', color: 'bg-red-600', borderColor: 'border-red-700', textColor: 'text-red-700' }
        ],
        get current() {
            return this.priorities[this.priorityIndex];
        },
        get progress() { 
            return (this.priorityIndex / (this.priorities.length - 1)) * 100; 
        },
        get trackStyle() { 
            return { width: `${this.progress}%` }; 
        },
        get thumbStyle() { 
            return { left: `${this.progress}%` }; 
        }
    }));

    Alpine.data('settingsTabs', () => ({
        tab: new URLSearchParams(window.location.search).get('tab') || 'profile',
        
        switchTab(newTab) {
            this.tab = newTab;
            window.history.replaceState(null, null, '?tab=' + newTab);
        }
    }));

    Alpine.data('contextMenu', () => ({
        top: 0,
        left: 0,
        
        calculatePosition(trigger) {
            if (!trigger) return;
            
            const rect = trigger.getBoundingClientRect();
            const menu = this.$refs.panel;
            if(!menu) return;
            
            const menuWidth = menu.offsetWidth;
            const menuHeight = menu.offsetHeight;
            
            // Margen de seguridad (8px)
            const margin = 8;
            
            // Posición inicial: abajo a la derecha del botón
            let targetTop = rect.bottom + margin;
            let targetLeft = rect.right - menuWidth;
            
            // Si se sale por abajo, lo mostramos arriba
            if (targetTop + menuHeight > window.innerHeight - margin) {
                targetTop = rect.top - menuHeight - margin;
            }
            
            // Ajustes horizontales para no salirse de la pantalla
            targetLeft = Math.max(margin, Math.min(targetLeft, window.innerWidth - menuWidth - margin));
            
            this.top = targetTop;
            this.left = targetLeft;
        }
    }));
};
