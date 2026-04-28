<div class="space-y-6">

    <!-- Preferencias de Aplicación -->
    <div class="bg-white p-6 md:p-8 shadow-sm rounded-xl border border-gray-100">
        <x-ui.section-header 
            title="Preferencias de Aplicación" 
            description="Ajusta cómo se muestra y funciona la interfaz para ti." 
            icon="monitor" 
        />

        <div class="space-y-6 max-w-xl">
             <!-- Idioma -->
             <div>
                 <x-ui.input-label for="language" value="Idioma Predeterminado" />
                 <select id="language" name="language" disabled class="mt-1 block w-full border border-gray-300 bg-gray-50 text-gray-500 px-3 py-2 text-sm rounded-md shadow-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900 cursor-not-allowed">
                     <option value="es" selected>Español (España)</option>
                     <option value="en">English (US)</option>
                     <option value="fr">Français</option>
                 </select>
                 <p class="mt-1 text-xs text-gray-400">Próximamente. Actualmente la aplicación está disponible solo en Español.</p>
             </div>

             <!-- Zona Horaria -->
             <div>
                 <x-ui.input-label for="timezone" value="Zona Horaria" />
                 <select id="timezone" name="timezone" class="mt-1 block w-full border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 rounded-md shadow-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900">
                     <option value="Europe/Madrid" selected>(GMT+01:00) Madrid</option>
                     <option value="America/New_York">(GMT-05:00) Eastern Time</option>
                     <option value="UTC">UTC</option>
                 </select>
             </div>
             
             <!-- Formato de fecha -->
             <div>
                 <fieldset>
                   <legend class="block font-medium text-sm text-gray-700 mb-2">Formato de Fecha</legend>
                   <div class="space-y-3">
                     <x-ui.radio name="date_format" value="d/m/Y" label="31/12/2026 (DD/MM/YYYY)" checked />
                     <x-ui.radio name="date_format" value="Y-m-d" label="2026-12-31 (YYYY-MM-DD)" />
                   </div>
                 </fieldset>
             </div>
             
             <div class="pt-2">
                 <x-ui.primary-button type="button" @click="$dispatch('notify', { message: 'Demo: Las preferencias se guardarán en el futuro', type: 'info' })">Guardar Preferencias</x-ui.primary-button>
             </div>
        </div>
    </div>

    <!-- Preferencias de Notificaciones -->
    <div class="bg-white p-6 md:p-8 shadow-sm rounded-xl border border-gray-100">
        <x-ui.section-header 
            title="Notificaciones" 
            description="Controla cuándo y cómo quieres recibir avisos o correos." 
            icon="bell" 
            iconColor="blue"
        />

        <div class="space-y-6 max-w-2xl">
             
            <!-- Switch Notification 1 -->
            <x-ui.setting-row 
                title="Notificaciones por Email" 
                description="Recibir resúmenes y alertas críticas en tu bandeja de entrada." 
                name="prefs-emails" 
                default="true" 
            />
            
            <div class="border-t border-gray-100"></div>
            
            <!-- Switch Notification 2 -->
            <x-ui.setting-row 
                title="Asignación de Tareas" 
                description="Aviso cuando alguien te asigna a una nueva tarea o proyecto." 
                name="prefs-assignments" 
                default="true" 
            />
            
             <div class="border-t border-gray-100"></div>
            
            <!-- Switch Notification 3 -->
            <x-ui.setting-row 
                title="Resumen Diario" 
                description="Un correo en la mañana con tus tareas pendientes del día." 
                name="prefs-digest" 
                default="false" 
            />

        </div>
    </div>
    
    <!-- Sistema y Metadatos -->
    <div class="bg-gray-50 p-6 md:p-8 shadow-sm rounded-xl border border-gray-200">
        <header class="mb-4">
             <h2 class="text-lg font-medium text-gray-700">Información del Sistema</h2>
        </header>
        
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6 text-sm">
            <div>
                <dt class="text-gray-500 mb-1">Versión de la App</dt>
                <dd class="font-medium text-gray-900">v1.2.0-beta</dd>
            </div>
            <div>
                <dt class="text-gray-500 mb-1">Entorno</dt>
                <dd class="font-medium text-gray-900">Producción</dd>
            </div>
             <div>
                <dt class="text-gray-500 mb-1">Exportar Datos de Cuenta</dt>
                <dd>
                     <button type="button" class="text-orange-600 hover:text-orange-700 font-medium hover:underline transition-colors focus:outline-none" @click="$dispatch('notify', { message: 'Generando archivo export.zip...', type: 'info' })">
                         Descargar .ZIP
                     </button>
                </dd>
            </div>
             <div>
                <dt class="text-gray-500 mb-1">Políticas</dt>
                <dd class="flex gap-3">
                     <a href="#" class="text-orange-600 hover:text-orange-700 hover:underline">Términos</a>
                     <span class="text-gray-300">•</span>
                     <a href="#" class="text-orange-600 hover:text-orange-700 hover:underline">Privacidad</a>
                </dd>
            </div>
        </dl>
    </div>

</div>
