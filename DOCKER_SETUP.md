# 🐳 TaskManager — Guía de despliegue con Docker (WSL + Docker Desktop)

## Requisitos previos

- **Docker Desktop** instalado y corriendo en Windows
- **WSL 2** habilitado (integración activada en Docker Desktop → Settings → Resources → WSL Integration)
- **Make** instalado en WSL (opcional): `sudo apt install make`
- Tu proyecto clonado dentro de WSL: `~/TaskMangerLaravel`

> ⚠️ **Importante:** Trabaja siempre desde la terminal WSL, no desde PowerShell.
> El rendimiento de volúmenes en WSL2 es mucho mejor que en rutas de Windows (`/mnt/c/...`).
>
> 💡 **VS Code:** Instala la extensión "WSL" y abre el proyecto con `WSL: Open Folder in WSL`.
> Editarás archivos de WSL exactamente igual que cualquier otro proyecto.

---

## Estructura de archivos Docker en el proyecto

```
TaskMangerLaravel/
├── Dockerfile                  ← Imagen PHP 8.4 con todas las extensiones
├── docker-compose.yml          ← Los 7 servicios del stack
├── .dockerignore               ← Evita copiar vendor/, node_modules/, etc.
├── .env.docker                 ← Template .env adaptado para Docker
├── .env.example                ← Template .env para entorno tradicional (Laragon/XAMPP)
├── vite.config.js              ← MODIFICADO: añade config HMR para Docker
├── Makefile                    ← Atajos de comandos (opcional)
└── docker/
    ├── entrypoint.sh           ← Script de arranque del contenedor PHP
    ├── php-fpm/
    │   └── www.conf            ← PHP-FPM escucha en 0.0.0.0:9000
    └── nginx/
        └── default.conf        ← Configuración de Nginx para Laravel
```

---

## Primera puesta en marcha

### Con Make
```bash
make setup
```

### Sin Make
```bash
cp .env.docker .env
docker compose up -d --build
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed
```

> El setup construye las imágenes, arranca los contenedores, genera el APP_KEY,
> ejecuta las migraciones y los seeders. Solo se hace una vez.

### ✅ Resultado esperado

| Servicio | URL |
|---|---|
| Aplicación Laravel | http://localhost:8000 |
| Vite HMR | http://localhost:5173 |
| Reverb WebSockets | ws://localhost:8080 |
| MySQL | localhost:3306 |
| Redis | localhost:6379 |

---

## Uso diario

| Acción | Con Make | Sin Make |
|---|---|---|
| Arrancar el stack | `make up` | `docker compose up -d` |
| Arrancar con logs | `make up-logs` | `docker compose up` |
| Parar todo | `make down` | `docker compose down` |
| Entrar al contenedor PHP | `make shell` | `docker compose exec app bash` |
| Ejecutar migraciones | `make migrate` | `docker compose exec app php artisan migrate` |
| Migraciones + seeders | `make seed` | `docker compose exec app php artisan migrate:fresh --seed` |
| Limpiar caché | `make cache-clear` | `docker compose exec app php artisan cache:clear` |
| Tinker | `make tinker` | `docker compose exec app php artisan tinker` |
| Ver todos los logs | `make logs` | `docker compose logs -f` |
| Ver logs de un servicio | `make logs-s s=worker` | `docker compose logs -f worker` |
| Abrir MySQL CLI | `make mysql` | `docker compose exec mysql mysql -u root -proot taskmanager` |
| Ver estado contenedores | `make ps` | `docker compose ps` |
| Reconstruir imágenes | `make rebuild` | `docker compose up -d --build` |
| Reset completo ⚠️ borra BD | `make reset` | `docker compose down -v` |

---

## Diferencias clave respecto a Laragon

| Concepto | Laragon | Docker |
|---|---|---|
| Servidor web | Apache | Nginx (contenedor) |
| PHP | Global en Windows | PHP 8.4 aislado en contenedor |
| MySQL | Servicio Windows | Contenedor con volumen persistente |
| Redis | Manual | Contenedor incluido |
| Queue worker | `composer dev` | Contenedor `worker` siempre activo |
| Reverb | `composer dev` | Contenedor `reverb` siempre activo |
| Vite HMR | `composer dev` | Contenedor `vite` siempre activo |

---

## Cambios en tu código existente

### `vite.config.js`
Añadida la sección `server` con `host: '0.0.0.0'` y `hmr.host: 'localhost'`.
Sin esto el Hot Module Replacement no funciona desde Docker Desktop.

### `.env`
Las diferencias principales respecto a tu `.env` de Laragon:

```dotenv
# En Laragon              →  En Docker
DB_HOST=127.0.0.1         →  DB_HOST=mysql
DB_PASSWORD=              →  DB_PASSWORD=root
REDIS_HOST=127.0.0.1      →  REDIS_HOST=redis
APP_URL=http://localhost   →  APP_URL=http://localhost:8000
REVERB_HOST=localhost      →  REVERB_HOST=localhost (el navegador conecta a localhost)
```

---

## Solución de problemas frecuentes

### Docker no arranca (`Cannot connect to Docker daemon`)
Docker Desktop no está corriendo en Windows.
Ábrelo y espera a que el icono de la ballena esté en verde, luego verifica:
```bash
docker info
```

### Puerto ya en uso (`port is already allocated`)
Algún proceso ocupa el puerto. Averigua cuál:
```bash
sudo lsof -i :8080   # o el puerto que falle
```
Si es un contenedor antiguo:
```bash
docker compose down
make up
```

### El contenedor `app` falla al arrancar
```bash
docker compose logs app
# Si hay error de permisos en storage/:
docker compose exec app chmod -R 775 storage bootstrap/cache
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### 502 Bad Gateway en http://localhost:8000
PHP-FPM no escucha en todas las interfaces. Verifica que existe `docker/php-fpm/www.conf`
con `listen = 0.0.0.0:9000` y que el `Dockerfile` lo copia correctamente.
Luego reconstruye:
```bash
make reset
make setup
```

### `MissingAppKeyException` (No application encryption key)
El APP_KEY está vacío en el `.env`:
```bash
# Con Make
make shell
php artisan key:generate

# Sin Make
docker compose exec app php artisan key:generate
```

### Vite no conecta (pantalla en blanco o assets rotos)
```bash
docker compose logs vite
# Asegúrate de que vite.config.js tiene la sección server con host: '0.0.0.0'
```

### Reverb no conecta
```bash
docker compose logs reverb
# Verifica en .env que REVERB_HOST=localhost (no el nombre del contenedor)
# El navegador conecta a localhost, no dentro de la red Docker
```

### Regenerar todo desde cero (⚠️ borra la BD)

Con Make:
```bash
make reset
make setup
```

Sin Make:
```bash
docker compose down -v
cp .env.docker .env
docker compose up -d --build
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed
```