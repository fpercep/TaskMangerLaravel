<x-app-layout>
    <div class="max-w-6xl mx-auto" x-data="settingsTabs">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Ajustes del Sistema</h1>

        <div class="flex flex-col md:flex-row gap-8">
            <!-- Sidebar / Pestañas Vertiacales -->
            <aside class="w-full md:w-64 shrink-0">
                <nav class="flex flex-col space-y-1">
                    <x-ui.tab-button name="profile" icon="user">
                        Mi Perfil
                    </x-ui.tab-button>
                    
                    <x-ui.tab-button name="config" icon="settings">
                        Configuración
                    </x-ui.tab-button>
                </nav>
            </aside>

            <!-- Contenido Principal -->
            <main class="flex-1 min-w-0">
                <!-- Tab: Mi Perfil -->
                <div x-show="tab === 'profile'" x-cloak x-transition.opacity.duration.300ms>
                    @include('pages.settings.partials.profile-tab')
                </div>

                <!-- Tab: Configuración -->
                <div x-show="tab === 'config'" x-cloak x-transition.opacity.duration.300ms style="display: none;">
                    @include('pages.settings.partials.config-tab')
                </div>
            </main>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('settingsTabs', () => ({
                tab: new URLSearchParams(window.location.search).get('tab') || 'profile',
                
                switchTab(newTab) {
                    this.tab = newTab;
                    window.history.replaceState(null, null, '?tab=' + newTab);
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>
