#!/bin/sh
set -e

# ─── Auto-instala dependencias PHP si no existen ──────────────────────────────
if [ ! -f "/var/www/vendor/autoload.php" ]; then
    echo "📦 Instalando dependencias de Composer..."
    composer install --no-interaction --prefer-dist
fi

# ─── Genera APP_KEY si no existe ──────────────────────────────────────────────
if grep -q "^APP_KEY=$" /var/www/.env 2>/dev/null; then
    echo "🔑 Generando APP_KEY..."
    php artisan key:generate
fi

# ─── Permisos de storage ──────────────────────────────────────────────────────
chmod -R 775 /var/www/storage /var/www/bootstrap/cache
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

exec "$@"
