# 🐳 TaskManager — Guía Docker

## Requisitos previos

- **Docker Desktop** corriendo
- **Make** disponible en tu terminal

| Entorno | Instalar Make |
|---|---|
| Windows + WSL 2 (recomendado) | `sudo apt install make` |
| Windows PowerShell | `winget install GnuWin32.Make` |
| Linux / Mac | Ya viene instalado |

> 💡 **Windows:** Trabaja siempre desde WSL 2, no desde PowerShell. El rendimiento
> de volúmenes es 3-5x mejor. Instala la extensión "WSL" en VS Code para editar
> archivos de WSL directamente desde Windows.

> ⚠️ **Linux / WSL:** Si Docker da error de permisos, ejecuta una sola vez:
> `sudo usermod -aG docker $USER` y reinicia la terminal.

---

## Primera puesta en marcha (solo una vez)

```bash
make setup
```

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

```bash
make up      # Arrancar
make down    # Parar
```

### Referencia de comandos

| Acción | Comando |
|---|---|
| Arrancar con logs | `make up-logs` |
| Entrar al contenedor PHP | `make shell` |
| Migraciones | `make migrate` |
| Migraciones + seeders | `make seed` |
| Limpiar caché | `make cache-clear` |
| Tinker | `make tinker` |
| Instalar paquete Composer | `make composer-require pkg=vendor/paquete` |
| Logs de todos los servicios | `make logs` |
| Logs de un servicio | `make logs-s s=worker` |
| MySQL CLI | `make mysql` |
| Estado de contenedores | `make ps` |
| Reconstruir imágenes | `make rebuild` |
| Reset completo ⚠️ borra BD | `make reset` |

---

## Estructura Docker

```
TaskMangerLaravel/
├── Dockerfile                  ← PHP 8.4 con todas las extensiones
├── docker-compose.yml          ← 7 servicios: app, nginx, worker, reverb, vite, mysql, redis
├── .dockerignore
├── .env.docker                 ← Template .env para Docker
├── .env.example                ← Template .env para Laragon/XAMPP
├── vite.config.js              ← Modificado para HMR en Docker
├── Makefile
└── docker/
    ├── entrypoint.sh
    ├── php-fpm/www.conf        ← PHP-FPM escucha en 0.0.0.0:9000
    └── nginx/default.conf
```

---

## Diferencias respecto a Laragon

| | Laragon | Docker |
|---|---|---|
| Servidor web | Apache | Nginx |
| PHP | Global en Windows | Aislado en contenedor |
| MySQL / Redis | Manual | Contenedor incluido |
| Queue worker / Reverb / Vite | `composer dev` | Contenedor siempre activo |

### Cambios en `.env`

```dotenv
DB_HOST=mysql                        
DB_PASSWORD=root                     
REDIS_HOST=redis                     
APP_URL=http://localhost:8000        
REVERB_HOST=localhost
REVERB_HOST_INTERNAL=reverb         
```

---

## Solución de problemas

### Docker no responde
Docker Desktop no está corriendo. Ábrelo, espera a que el icono esté en verde y verifica con `docker info`.

### Puerto en uso (`port is already allocated`)
```bash
sudo lsof -i :8080    # Averigua qué proceso ocupa el puerto
docker compose down   # Si es un contenedor antiguo
make up
```

### Permisos en `storage/`
```bash
docker compose exec app chmod -R 775 storage bootstrap/cache
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### 502 Bad Gateway
Reconstruye desde cero:
```bash
make reset && make setup
```

### `MissingAppKeyException`
```bash
make shell
php artisan key:generate
```

### Vite no conecta
Verifica que `vite.config.js` tiene `server.host: '0.0.0.0'`.

### Reverb no conecta
Verifica en `.env` que `REVERB_HOST=localhost` y `REVERB_HOST_INTERNAL=reverb`.

### Reset completo
```bash
make reset
make setup
```
