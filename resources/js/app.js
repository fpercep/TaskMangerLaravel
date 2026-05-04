import './bootstrap';

import Alpine from 'alpinejs';
import persist from '@alpinejs/persist';
import collapse from '@alpinejs/collapse';

Alpine.plugin(persist);
Alpine.plugin(collapse);

window.Alpine = Alpine;

// Registrar componentes globales
import ui from './alpine/ui';
import kanban from './alpine/kanban';

ui();
kanban();

// Helpers globales
Alpine.magic('formatDate', () => {
    return (dateString) => {
        if (!dateString) return 'Sin definir';
        const date = new Date(dateString.replace(' ', 'T'));
        if (isNaN(date.getTime())) return dateString;
        return date.toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    };
});

Alpine.start();
