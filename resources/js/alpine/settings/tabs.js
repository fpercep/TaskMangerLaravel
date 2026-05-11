export default () => ({
    tab: new URLSearchParams(window.location.search).get('tab') || 'profile',
    
    switchTab(newTab) {
        this.tab = newTab;
        window.history.replaceState(null, null, '?tab=' + newTab);
    }
});
