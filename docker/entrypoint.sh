#!/bin/sh
set -e

# ─── Auto-instala dependencias PHP si no existen ──────────────────────────────
if [ ! -d "/var/www/vendor" ]; then
    echo "📦 Instalando dependencias de Composer..."
    composer install --no-interaction --prefer-dist
fi

# ─── Genera APP_KEY si no existe ──────────────────────────────────────────────
if grep -q "^APP_KEY=$" /var/www/.env 2>/dev/null; then
    echo "🔑 Generando APP_KEY..."
    php artisan key:generate
fi

exec "$@"
