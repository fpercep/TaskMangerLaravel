import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],

    // ─── [DOCKER] Necesario para que HMR funcione desde WSL/Docker Desktop ───
    server: {
        host: '0.0.0.0',       // Escucha en todas las interfaces del contenedor
        port: 5173,
        hmr: {
            host: 'localhost',  // El navegador conecta a localhost (Docker Desktop hace el bridge)
        },
    },
})
