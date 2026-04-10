import './bootstrap';

import Alpine from 'alpinejs';
import persist from '@alpinejs/persist';
import collapse from '@alpinejs/collapse';

Alpine.plugin(persist);
Alpine.plugin(collapse);

window.Alpine = Alpine;

// Registrar componentes globales
import ui from './alpine/ui';
ui();

Alpine.start();
