export default () => ({
    showSuggestions: window.Alpine.$persist(true).as('suggestions_panel_visible'),
    
    toggleSuggestions() {
        this.showSuggestions = !this.showSuggestions;
    }
});
