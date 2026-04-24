# Reporte de Auditoría: Módulo de Estructura de Proyectos

Este documento detalla las inconsistencias lógicas, ineficiencias técnicas y desviaciones de los estándares de desarrollo identificadas en el módulo de Proyectos y la visualización Kanban.

---

## 1. Interpretación del Análisis
Se ha evaluado la implementación del `ProjectController`, el modelo `Project` y la vista Kanban (`pages.projects.show`). El análisis se centra en la integridad de las relaciones de datos, la eficiencia de las consultas de base de datos y la adherencia a la arquitectura "Backend-Frontend" definida.

---

## 2. Hallazgos y Desviaciones

### A. Lógica y Relaciones de Datos

#### F1: Error de Jerarquía en Tablero Kanban
- **Archivo:** `app/Http/Controllers/ProjectController.php` (Línea 43) y `resources/views/pages/projects/show.blade.php`.
- **Fallo:** El controlador carga todas las tareas del proyecto (`$project->tasks`) sin filtrar por jerarquía.
- **Regla Incumplida:** Lógica de Negocio / Modelado - Desviación del estándar de integridad referencial.
- **Justificación:** Al no filtrar por `parent_id IS NULL`, las subtareas se muestran como tarjetas independientes en las columnas del Kanban. Esto rompe la lógica de negocio donde las subtareas deberían estar contenidas o vinculadas a una tarea padre, provocando ruido visual y duplicidad lógica en el tablero.

#### F2: Inconsistencia en Nomenclatura de Roles (Pivot)
- **Archivo:** `app/Http/Controllers/ProjectController.php` (Línea 27) vs `app/Models/Project.php` (Líneas 51-70).
- **Fallo:** El controlador asigna el rol `admin` al crear el proyecto, pero el modelo `Project` solo define scopes y relaciones para `viewers`, `editors` y `managers`.
- **Regla Incumplida:** Consistencia Arquitectónica / Honradez Intelectual.
- **Justificación:** Existe una discrepancia entre los datos insertados y la interfaz del modelo. El rol `admin` no tiene métodos de acceso o filtrado en el modelo, lo que lo convierte en un "valor huérfano" que puede causar errores en la gestión de permisos.

---

### B. Ineficiencia en Consultas y Rendimiento

#### F3: Carga Masiva de Tareas sin Filtrado ni Paginación
- **Archivo:** `app/Http/Controllers/ProjectController.php` (Línea 43).
- **Fallo:** Uso de `load(['tasks' => ...])` para obtener la totalidad de las tareas del proyecto.
- **Regla Incumplida:** Optimización Proactiva - "Si detectas que el método propuesto es funcional pero ineficiente, es obligatorio presentar una alternativa...".
- **Justificación:** Para proyectos con un histórico extenso, cargar cientos de tareas (incluyendo completadas de meses anteriores) en cada visualización del Kanban penaliza la memoria del servidor y el tiempo de respuesta del frontend.

---

### C. Renderizado y Arquitectura Blade/Alpine

#### F4: Inyección de Modelos Eloquent en Vistas y Componentes
- **Archivo:** `resources/views/pages/projects/show.blade.php` (Líneas 1, 5, 49).
- **Fallo:** Paso del objeto `$project` completo tanto a la página principal como al componente modal `<x-modals.create-task>`.
- **Regla Incumplida:** Interacción Backend-Frontend - "HACER: Pasar los datos estrictamente procesados desde el Controlador... hacia la vista. Blade solo debe contener directivas de control...".
- **Justificación:** Al inyectar el modelo completo, se delega la responsabilidad de "procesamiento" a la vista, permitiendo que esta acceda a métodos internos del modelo o active consultas accidentales (N+1), violando la separación de capas.

#### F5: Arquitectura Híbrida Confusa (Blade-in-Alpine)
- **Archivo:** `resources/views/components/kanban/column.blade.php` (Línea 24).
- **Fallo:** Inclusión del componente Blade `<x-kanban.card />` dentro de un `<template x-for>` de Alpine.js.
- **Regla Incumplida:** Estándares Alpine.js - "Confiar enteramente en la reactividad de Alpine... para reflejar los cambios de estado en el DOM".
- **Justificación:** Aunque funcional, esta mezcla oscurece la procedencia de los datos. El componente Blade se renderiza vacío o con datos por defecto en el servidor, y luego Alpine debe "llenarlo" en el cliente. Esto dificulta el mantenimiento y la depuración del ciclo de vida del DOM.

---

### D. Seguridad y Estándares de Diseño

#### F6: Autorización Comentada (Vulnerabilidad)
- **Archivo:** `app/Http/Controllers/ProjectController.php` (Línea 41).
- **Fallo:** La instrucción `$this->authorize('view', $project);` se encuentra comentada.
- **Regla Incumplida:** Directivas de Seguridad / Honradez Intelectual.
- **Justificación:** Cualquier usuario autenticado puede visualizar cualquier proyecto simplemente alterando el ID en la URL, lo que representa una brecha de seguridad crítica en la gestión de accesos.

#### F7: Uso de Valores Arbitrarios en Tailwind
- **Archivo:** `resources/views/components/kanban/card.blade.php` (Línea 5).
- **Fallo:** Uso de la clase `border-l-[6px]`.
- **Regla Incumplida:** Tailwind CSS - "EVITAR: Usar valores arbitrarios excesivos... HACER: Extender el archivo tailwind.config.js (o @theme)".
- **Justificación:** Un borde de 6px no forma parte del sistema de diseño definido en `app.css`. Esto rompe la consistencia visual y la escalabilidad del sistema de temas.

---

## 3. Conclusión
El módulo de Estructura de Proyectos es funcional pero carece de robustez arquitectónica. Los problemas de **jerarquía de datos** (subtareas sueltas) y la **ausencia de autorización activa** son los puntos más críticos. Asimismo, la falta de transformación de datos (DPO/ViewModels) antes de llegar a Blade contraviene los estándares de limpieza y rendimiento establecidos para el proyecto.
