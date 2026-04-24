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
};
