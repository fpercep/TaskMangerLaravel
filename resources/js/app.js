import './bootstrap';

import Alpine from 'alpinejs';
import persist from '@alpinejs/persist';
import collapse from '@alpinejs/collapse';

Alpine.plugin(persist);
Alpine.plugin(collapse);

window.Alpine = Alpine;

// Registrar componentes globales
import kanbanStore from './alpine/kanban/kanban-store';
import kanbanBoard from './alpine/kanban/kanban-board';
import taskDetailModal from './alpine/task-detail/task-detail-modal';
import membersStore from './alpine/project-members/members-store';
import projectMembers from './alpine/project-members/project-members';
import userSearch from './alpine/project-members/user-search';
import sidebar from './alpine/sidebar/sidebar';
import echoListeners from './alpine/echo-listeners';

// Módulos UI extraídos
import accordion from './alpine/ui/accordion';
import modalState from './alpine/ui/modal-state';
import contextMenu from './alpine/ui/context-menu';
import prioritySlider from './alpine/tasks/priority-slider';
import settingsTabs from './alpine/settings/tabs';
import layoutPanel from './alpine/layout/panel';

Alpine.store('members', membersStore());
Alpine.store('kanban', kanbanStore());
Alpine.data('projectMembers', projectMembers);
Alpine.data('userSearch', userSearch);
Alpine.data('sidebar', sidebar);

Alpine.data('accordion', accordion);
Alpine.data('modalState', modalState);
Alpine.data('contextMenu', contextMenu);
Alpine.data('prioritySlider', prioritySlider);
Alpine.data('settingsTabs', settingsTabs);
Alpine.data('layoutPanel', layoutPanel);

Alpine.data('kanbanBoard', kanbanBoard);
Alpine.data('taskDetailModal', taskDetailModal);

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

// Inicializar listeners de WebSockets (Reverb)
echoListeners(window.AppUserId);

Alpine.start();
