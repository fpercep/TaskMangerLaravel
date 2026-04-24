# Reporte de Auditoría: Módulo de Gestión de Tareas

Este documento presenta un análisis crítico de la implementación de tareas, evaluando la lógica de controladores, la reactividad en Alpine.js y la integridad de los componentes Blade bajo los estándares @user_global y @reglas-desarrollo.md.

---

## 1. Interpretación del Análisis
Se ha auditado el flujo completo de una tarea: desde su creación en el modal hasta su gestión en el tablero Kanban. El análisis se enfoca en la consistencia de los estados, la seguridad de las transiciones y la eficiencia de la comunicación entre el backend y el frontend.

---

## 2. Hallazgos y Desviaciones

### A. Ciclo de Vida y Gestión de Estados

#### F1: Vulnerabilidad Crítica en Transición de Estados
- **Archivo:** `app/Http/Controllers/TaskController.php` (Línea 39).
- **Fallo:** La instrucción de autorización `$this->authorize('update', $task);` está comentada en el método `updateStatus`.
- **Regla Incumplida:** Directivas de Seguridad / Honradez Intelectual.
- **Justificación:** Un usuario malintencionado podría modificar el estado de cualquier tarea del sistema (incluso de proyectos ajenos) mediante peticiones directas al endpoint de actualización, ya que solo se verifica la autenticación básica pero no la propiedad o permisos sobre el recurso.

#### F2: Inconsistencia en Lógica de Vencimiento (Frontend vs Backend)
- **Archivo:** `app/Models/Task.php` (Línea 118) vs `resources/js/alpine/kanban.js` (Línea 42).
- **Fallo:** La lógica para determinar si una tarea está "vencida" es dispar. El backend usa `now()` (incluye hora), mientras el frontend utiliza `today.setHours(0, 0, 0, 0)` (ignora hora).
- **Regla Incumplida:** Lógica de Negocio / Integridad de Datos.
- **Justificación:** Esta discrepancia causa que una tarea que vence "hoy" pueda aparecer como vencida en el backend (si la hora actual superó la hora de creación/corte) pero como al día en el frontend, generando confusión en el usuario y falta de confianza en la interfaz.

#### F3: Gestión de Relaciones Incompleta en Creación
- **Archivo:** `app/Http/Controllers/TaskController.php` (Línea 24) y `app/Models/Task.php`.
- **Fallo:** El método `store` no contempla el campo `parent_id`, a pesar de que el modelo y la base de datos soportan subtareas.
- **Regla Incumplida:** Ciclo de Vida de la Tarea / Optimización Proactiva.
- **Justificación:** Se está limitando la funcionalidad del sistema por una omisión en el controlador, impidiendo la creación de jerarquías de tareas desde la interfaz principal.

---

### B. Reactividad y Frontend (Alpine.js)

#### F4: Mezcla de Modelos de Interacción (UX Jarring)
- **Archivo:** `resources/views/components/modals/create-task.blade.php` (Línea 19) vs `resources/js/alpine/kanban.js` (Línea 84).
- **Fallo:** La creación de tareas utiliza una petición sincrónica (POST tradicional con recarga de página), mientras que el cambio de estado en el Kanban utiliza AJAX.
- **Regla Incumplida:** Ineficiencia en la reactividad del frontend.
- **Justificación:** La recarga completa de la página tras crear una tarea rompe la fluidez de la aplicación tipo SPA que intenta proyectar el tablero Kanban, resultando en una experiencia de usuario inconsistente.

#### F5: Lógica de Estado en Atributos HTML
- **Archivo:** `resources/views/components/modals/create-task.blade.php` (Líneas 3-8).
- **Fallo:** Definición de un objeto complejo y métodos (`onOpen`, `status`) directamente en el atributo `alpine-data` del componente `x-ui.dialog`.
- **Regla Incumplida:** Alpine.js - "EVITAR: Escribir funciones complejas... directamente dentro de los atributos x-data. HACER: Extraer... usando Alpine.data()".
- **Justificación:** Dificulta la reutilización de la lógica del modal y ensucia el marcado HTML, contraviniendo el estándar de externalización de lógica JS.

---

### C. Estándares de Blade y UI

#### F6: Uso de Elementos Nativos y Clases Hardcodeadas
- **Archivo:** `resources/views/components/modals/create-task.blade.php` (Líneas 38, 42).
- **Fallo:** Uso de etiquetas `<input type="radio">` nativas con clases manuales en lugar de componentes Blade.
- **Regla Incumplida:** Arquitectura General y Blade - "HACER: Extraer cualquier elemento de la interfaz... (inputs) en Componentes de Blade".
- **Justificación:** Al no usar un componente `<x-ui.radio>`, se pierde la capacidad de actualizar globalmente el estilo de los selectores de estado y se aumenta la redundancia de clases en el HTML.

#### F7: Valores Arbitrarios y Desviación de Tokens
- **Archivo:** `resources/views/components/ui/priority-slider.blade.php` (Líneas 9, 51) y `create-task.blade.php` (Línea 26).
- **Fallo:** Uso de `text-[11px]`, `text-[10px]` y `focus:border-orange-500`.
- **Regla Incumplida:** Tailwind CSS - "EVITAR: Usar valores arbitrarios excesivos...".
- **Justificación:** El uso de tamaños de fuente fuera de la escala de Tailwind (`text-xs`, `text-sm`) y el hardcoding de colores de enfoque (en lugar de usar variables de tema) fragmenta el sistema de diseño y dificulta la implementación de temas (ej. Dark Mode).

---

## 3. Conclusión
El módulo de Gestión de Tareas presenta deficiencias críticas de **seguridad por omisión de autorización** y una **experiencia de usuario fragmentada** debido a la mezcla de interacción sincrónica y asincrónica. La inconsistencia en la lógica de vencimiento entre el servidor y el cliente es un fallo de integridad que debe ser abordado para garantizar la fiabilidad del sistema.
