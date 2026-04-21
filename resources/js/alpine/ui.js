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
};
