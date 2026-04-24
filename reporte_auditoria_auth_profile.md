# Reporte de Auditoría: Módulo de Autenticación y Perfil

Este documento detalla las desviaciones arquitectónicas, malas prácticas y fallos de seguridad encontrados en la implementación de los módulos de Autenticación, Perfil y Ajustes, evaluados bajo las directivas de @user_global y @reglas-desarrollo.md.

---

## 1. Interpretación del Análisis
Se ha realizado una revisión exhaustiva de los controladores de autenticación (`Auth/*`), el `ProfileController`, `SettingsController`, y sus respectivas vistas en `resources/views/pages/settings/` y `resources/views/auth/`. El objetivo es identificar inconsistencias con el stack Laravel + Tailwind + Alpine y desviaciones de los estándares de arquitectura definidos.

---

## 2. Hallazgos y Desviaciones

### A. Arquitectura de Vistas (Blade)

#### F1: Lógica de Negocio y Estado en Blade
- **Archivo:** `resources/views/pages/settings/partials/profile-tab.blade.php` (Línea 47, 140)
- **Fallo:** Evaluación de interfaces (`instanceof`) y estados complejos (`$errors->userDeletion->isNotEmpty()`) directamente en la directiva `@if` y atributos de componentes.
- **Regla Incumplida:** Arquitectura General y Blade - "EVITAR: Escribir lógica de negocio... dentro de las vistas Blade. HACER: Pasar los datos estrictamente procesados desde el Controlador o ViewModels hacia la vista."
- **Justificación:** El uso de `instanceof` y métodos de verificación de estado en Blade aumenta el acoplamiento entre la vista y la lógica de negocio, dificultando el mantenimiento y violando la separación de responsabilidades.

#### F2: Formateo de Datos en Blade
- **Archivo:** `resources/views/pages/settings/partials/profile-tab.blade.php` (Línea 12)
- **Fallo:** Uso de `translatedFormat('M Y')` sobre un objeto `Carbon` directamente en la vista.
- **Regla Incumplida:** Arquitectura General y Blade - "HACER: Pasar los datos estrictamente procesados desde el Controlador... hacia la vista."
- **Justificación:** El formateo de fechas es lógica de presentación que debe ser resuelta en el backend para que la vista reciba strings listos para mostrar.

#### F3: Repetición de Bloques HTML y Clases Tailwind
- **Archivo:** `resources/views/pages/settings/partials/config-tab.blade.php` (Líneas 62-90, 102-126)
- **Fallo:** Estructuras repetitivas para los switches de notificación e información del sistema con las mismas clases de Tailwind.
- **Regla Incumplida:** Arquitectura General y Blade - "EVITAR: Repetir bloques de código HTML con las mismas clases de Tailwind. HACER: Extraer... en Componentes de Blade."
- **Justificación:** La repetición manual de estas estructuras incrementa el riesgo de inconsistencia visual y dificulta cambios globales en el diseño.

#### F4: Uso de Inputs Nativos en lugar de Componentes
- **Archivo:** `resources/views/auth/login.blade.php` (Línea 30)
- **Fallo:** Uso de un `<input type="checkbox">` nativo con clases de Tailwind hardcodeadas.
- **Regla Incumplida:** Arquitectura General y Blade - "HACER: Extraer cualquier elemento de la interfaz que se use más de una vez (botones, tarjetas, modales, inputs) en Componentes de Blade."
- **Justificación:** Inconsistencia con el resto del sistema que utiliza componentes para inputs de texto y botones, rompiendo la centralización del diseño.

---

### B. Interactividad y Estado (Alpine.js)

#### F5: Lógica de Componente Alpine Definida en Blade
- **Archivo:** `resources/views/pages/settings/index.blade.php` (Líneas 36-45)
- **Fallo:** Definición de `Alpine.data('settingsTabs', ...)` dentro de una etiqueta `<script>` en el archivo Blade.
- **Regla Incumplida:** Alpine.js - "HACER: Extraer toda lógica de estado que supere una instrucción simple usando Alpine.data(). Define la lógica en un archivo JavaScript externo e inicialízala en el HTML."
- **Justificación:** Fragmenta la lógica de frontend en múltiples archivos Blade, impidiendo la minificación centralizada y dificultando el debug global.

#### F6: Manipulación Directa y Eventos Inline
- **Archivo:** `resources/views/pages/settings/partials/config-tab.blade.php` (Líneas 45, 113)
- **Fallo:** Uso de atributos `onclick` con llamadas a `alert()` de JavaScript.
- **Regla Incumplida:** Alpine.js - "HACER: Confiar enteramente en la reactividad de Alpine... para reflejar los cambios de estado en el DOM." (Directiva implícita sobre evitar JS inline no reactivo).
- **Justificación:** El uso de JS inline rompe la arquitectura reactiva de Alpine.js y el principio de "utility-first" aplicado al comportamiento.

---

### C. Backend e Interacción (Controllers)

#### F7: Inyección de Modelos Eloquent Completos en la Vista
- **Archivo:** `app/Http/Controllers/SettingsController.php` (Línea 16) y `app/Http/Controllers/ProfileController.php` (Línea 20).
- **Fallo:** Se pasa el objeto `$user` (Eloquent Model) directamente a la vista.
- **Regla Incumplida:** Interacción Backend-Frontend - "HACER: Transformar los datos usando Eloquent API Resources o Arrays limpios en el controlador antes de inyectarlos..." (Regla 4).
- **Justificación:** Aunque no se envía directamente a Alpine vía `@js()`, pasar el modelo completo a la vista permite que esta realice consultas adicionales (N+1) o acceda a atributos sensibles que no deberían estar disponibles en la capa de presentación.

#### F8: Lógica de Validación Inline en Controlador
- **Archivo:** `app/Http/Controllers/Auth/RegisteredUserController.php` (Líneas 33-37).
- **Fallo:** Uso de `$request->validate([...])` directamente en el método `store`.
- **Regla Incumplida:** Arquitectura General - "HACER: Pasar los datos estrictamente procesados desde el Controlador..." (Desviación del estándar de usar FormRequests para coherencia con `ProfileUpdateRequest`).
- **Justificación:** Inconsistencia arquitectónica. Mientras el perfil usa un `ProfileUpdateRequest`, el registro mantiene la lógica acoplada al controlador.

---

## 3. Conclusión
El módulo presenta una base funcional sólida basada en Laravel Breeze, pero incumple sistemáticamente los estándares de **limpieza de Blade** (lógica dentro de vistas) y **externalización de Alpine.js**. La mayor vulnerabilidad arquitectónica reside en el **acoplamiento de los modelos Eloquent con las vistas**, lo que compromete la escalabilidad y la seguridad por exposición de datos no procesados.
