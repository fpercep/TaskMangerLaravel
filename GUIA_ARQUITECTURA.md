# Guía Detallada de Arquitectura y Rutas de la Aplicación

Esta guía proporciona una visión completa de la arquitectura de la aplicación **TaskManager**, estructurada en las seis capas principales que la componen. Se detalla cómo interactúan las distintas tecnologías utilizadas (Laravel, Docker, Alpine.js, Tailwind CSS 4, Reverb, Redis) y qué papel desempeña cada componente en el ciclo de vida de una petición.

---

## 1. Capa de Infraestructura y Datos (Estructural)

Esta capa define cómo se ejecuta la aplicación y cómo se almacenan los datos de forma persistente. La infraestructura se apoya fuertemente en **Docker** para ofrecer un entorno de desarrollo aislado y predecible.

### Infraestructura (Docker)
El archivo `docker-compose.yml` orquesta la inicialización de 7 servicios clave:
- **`app` (PHP-FPM):** El núcleo de Laravel. Procesa el código PHP.
- **`nginx`:** El servidor web expuesto en el puerto `8000`. Actúa como proxy inverso hacia el contenedor `app`.
- **`mysql`:** Base de datos relacional (MySQL 8.0) mapeada al puerto `3306`. Almacena toda la información persistente.
- **`redis`:** Almacén en memoria (puerto `6379`) que gestiona la caché, sesiones y hace de intermediario para el sistema de colas.
- **`worker`:** Contenedor dedicado exclusivamente a ejecutar `php artisan queue:work redis`, procesando trabajos en segundo plano sin bloquear el servidor web.
- **`reverb`:** Servidor de WebSockets de Laravel (puerto `8080`) que permite enviar eventos en tiempo real al frontend.
- **`vite`:** Servidor de desarrollo frontend (puerto `5173`) que compila assets y ofrece Hot Module Replacement (HMR).

### Capa de Datos (Modelos)
Ubicados en `app/Models/`, son la representación orientada a objetos de la base de datos (Active Record):
- **`User`:** Usuarios de la aplicación.
- **`Project`:** Proyectos creados por los usuarios (relación con miembros).
- **`Task`:** Tareas asociadas a un proyecto.
- **`TaskStep`:** Pasos o sub-ítems específicos dentro de una tarea.

---

## 2. Capa de Enrutamiento y Middleware (Estructural)

Esta capa recibe las solicitudes HTTP o conexiones de WebSocket y las dirige a la lógica de negocio (Controladores o Canales).

### Rutas Web (`routes/web.php`)
Protegidas casi en su totalidad por el middleware `auth` (requiere que el usuario haya iniciado sesión). 
- **Navegación General:**
  - `GET /`, `GET /dashboard`: Vista principal de los proyectos.
  - `GET /mi-dia`: Vista enfocada en las tareas del día (`MyDayController`).
- **Proyectos (`ProjectController`):**
  - `POST /projects`, `GET /projects/{project}`, `PUT /projects/{project}`, `DELETE /projects/{project}`
- **Miembros de Proyecto (`ProjectMemberController`):**
  - Permite gestionar qué usuarios tienen acceso a un proyecto. 
  - `POST /projects/{project}/members`, `DELETE /projects/{project}/members/{user}`, etc.
- **Tareas y Pasos (`TaskController`, `TaskStepController`):**
  - `POST /projects/{project}/tasks`: Crear tarea.
  - `PATCH /tasks/{task}`, `DELETE /tasks/{task}`: Modificar o eliminar tarea.
  - `POST /tasks/{task}/steps`, `PATCH /steps/{step}/toggle`: Gestión de sub-tareas.
- **Ajustes y Perfil:**
  - `GET /settings`: Unificado con `SettingsController`.
  - Rutas bajo `/profile` para editar la información del usuario (`ProfileController`).

### Rutas de WebSockets (`routes/channels.php`)
Gestionan la autorización para suscribirse a eventos en tiempo real.
- `App.Models.User.{id}`: Autoriza a los usuarios para escuchar eventos privados relacionados a su cuenta.

---

## 3. Capa de Backend (Dominio)

La lógica de negocio reside en los controladores (`app/Http/Controllers/`), que procesan los datos enviados por la vista, interactúan con los modelos, despachan eventos y devuelven la respuesta (generalmente vistas Blade, o fragmentos si se usa Alpine Ajax).

- **`ProjectController` / `TaskController` / `TaskStepController`:** Son el centro de las operaciones CRUD de las entidades del dominio. Después de cualquier mutación de datos (crear, actualizar, eliminar), se guardan los cambios en la base de datos y se disparan eventos que posteriormente la Capa de Asincronía manejará.
- **`DashboardController` / `MyDayController`:** Encargados de recopilar la información necesaria de múltiples modelos (Proyectos y Tareas asignadas/destacadas) para inyectarla en las vistas de Blade.

---

## 4. Capa de Asincronía y Sincronización (Broadcasting & Queues)

Esta es una de las partes más críticas para garantizar que la aplicación sea rápida (no bloqueando peticiones) y colaborativa (actualizaciones en tiempo real).

### Cola de Trabajos (Queues con Redis)
Para tareas pesadas o que no requieran respuesta inmediata al cliente, se envían a la cola. El contenedor **`worker`** toma estos trabajos de Redis y los ejecuta en segundo plano.

### Broadcasting (Laravel Reverb)
Cuando ocurren acciones importantes en el Backend, se despachan eventos ubicados en `app/Events/Task/`:
- `TaskCreated`
- `TaskUpdated`
- `TaskDeleted`
- `TaskAssigned`
- `TaskMoved`
- `TaskStepsUpdated`

Estos eventos implementan la interfaz `ShouldBroadcast`, lo que indica a Laravel que envíe la información de este evento (vía Redis y procesado por el worker) al servidor **Reverb**. Reverb entonces emite estos datos a cualquier cliente del frontend que esté escuchando el canal correspondiente.

---

## 5. Capa de Frontend (Reactividad: Blade + Alpine.js)

El frontend está construido sobre una arquitectura **"HTML-over-the-wire"** y reactividad ligera, prescindiendo de frameworks pesados como React o Vue.

- **Vistas (Blade):** Las páginas base se renderizan desde el servidor utilizando el motor de plantillas de Laravel en `resources/views/`.
- **Estilos (Tailwind CSS 4):** Utilizado para todo el diseño visual, integrado directamente en el build de Vite.
- **Reactividad (Alpine.js):** 
  - Usado para el estado local de los componentes (modales, menús desplegables, pestañas).
  - Integrado con plugins adicionales: `@alpinejs/collapse` (animaciones), `@alpinejs/morph` (para transiciones suaves en el DOM), y `@alpinejs/persist`.
- **Interacciones asíncronas (`@imacrayon/alpine-ajax`):** Permite enviar peticiones HTTP desde el frontend y actualizar fragmentos del DOM sin recargar toda la página, imitando la experiencia de una SPA (Single Page Application).
- **Tiempo Real (Laravel Echo + Pusher-js):** Se conectan al servidor **Reverb** (puerto `8080`). Cuando escuchan los eventos del punto 4 (`TaskCreated`, `TaskUpdated`, etc.), ejecutan código de Alpine/JavaScript para actualizar el estado visual de la interfaz de forma inmediata para todos los miembros conectados al proyecto.

---

## 6. Capa de Despliegue

La aplicación está completamente dockerizada, lo cual facilita no solo el desarrollo, sino un potencial despliegue a producción.

- **`Dockerfile`:** Contiene las instrucciones para construir la imagen de PHP con todas las extensiones necesarias (pdo_mysql, redis, etc.) tanto para el servidor web (`app`) como para el procesador de colas (`worker`) y servidor de websockets (`reverb`).
- **`Makefile`:** Agrupa comandos útiles (`make setup`, `make up`, `make down`, `make shell`) para automatizar el ciclo de vida del desarrollo e inicialización de contenedores, sin que el desarrollador tenga que escribir comandos de `docker-compose` o `artisan` complejos manualmente.
- **Entorno de Red:** Los servicios están conectados a una red bridge interna de Docker (`taskmanager`), protegiendo los puertos internos y exponiendo solo el puerto HTTP (`8000`), el WebSocket (`8080`) y el de Vite (`5173`) hacia la máquina anfitriona.
