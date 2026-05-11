export default () => ({
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
});
