# ─── TaskManager — Comandos Docker de desarrollo ─────────────────────────────
# Uso: make <comando>

COMPOSE = docker compose
APP     = $(COMPOSE) exec app

# ─── Arranque y parada ────────────────────────────────────────────────────────

## Primera vez: construye imágenes, arranca todo y prepara la BD
setup:
	cp .env.docker .env
	$(COMPOSE) up -d --build
	@echo "⏳ Esperando a que el contenedor app esté listo..."
	@until $(COMPOSE) exec -T app php -r "require '/var/www/vendor/autoload.php';" 2>/dev/null; do sleep 2; done
	$(APP) php artisan key:generate
	$(APP) php artisan migrate --force
	$(APP) php artisan db:seed
	@echo ""
	@echo "✅ TaskManager listo en http://localhost:8000"

## Arrancar todos los servicios
up:
	$(COMPOSE) up -d

## Arrancar y ver logs en tiempo real
up-logs:
	$(COMPOSE) up

## Parar todos los servicios
down:
	$(COMPOSE) down

## Parar y eliminar volúmenes (⚠️ borra la BD)
reset:
	$(COMPOSE) down -v

## Reconstruir imágenes desde cero
rebuild:
	$(COMPOSE) up -d --build

# ─── Laravel ─────────────────────────────────────────────────────────────────

## Abrir shell dentro del contenedor PHP
shell:
	$(APP) bash

## Ejecutar migraciones
migrate:
	$(APP) php artisan migrate

## Ejecutar migraciones + seeders
seed:
	$(APP) php artisan migrate:fresh --seed

## Limpiar caché de Laravel
cache-clear:
	$(APP) php artisan cache:clear
	$(APP) php artisan config:clear
	$(APP) php artisan route:clear
	$(APP) php artisan view:clear

## Ejecutar tinker
tinker:
	$(APP) php artisan tinker

# ─── Composer ────────────────────────────────────────────────────────────────

## Instalar dependencias PHP
composer-install:
	$(APP) composer install

## Añadir paquete: make composer-require pkg=nombre/paquete
composer-require:
	$(APP) composer require $(pkg)

# ─── NPM ─────────────────────────────────────────────────────────────────────

## Instalar dependencias JS
npm-install:
	$(COMPOSE) exec vite npm install

# ─── Logs ────────────────────────────────────────────────────────────────────

## Ver logs de todos los servicios
logs:
	$(COMPOSE) logs -f

## Ver logs de un servicio: make logs-s s=worker
logs-s:
	$(COMPOSE) logs -f $(s)

# ─── Base de datos ───────────────────────────────────────────────────────────

## Abrir MySQL CLI
mysql:
	$(COMPOSE) exec mysql mysql -u root -proot taskmanager

# ─── Estado ──────────────────────────────────────────────────────────────────

## Ver estado de todos los contenedores
ps:
	$(COMPOSE) ps

## Ejecutar pruebas (e.g. make test filter=LeaveProjectTest)
test:
ifdef filter
	$(APP) php artisan test --filter="$(filter)"
else
	$(APP) php artisan test
endif

.PHONY: setup up up-logs down reset rebuild shell migrate seed cache-clear \
        tinker composer-install composer-require npm-install logs logs-s mysql ps test
