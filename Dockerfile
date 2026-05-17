FROM php:8.2-fpm

# ─── Dependencias del sistema ─────────────────────────────────────────────────
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libpng-dev libonig-dev libxml2-dev libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# ─── Extensiones PHP ──────────────────────────────────────────────────────────
RUN docker-php-ext-install \
    pdo_mysql mbstring exif pcntl bcmath gd zip

# Redis (necesario para queues y caché)
RUN pecl install redis && docker-php-ext-enable redis

# ─── Composer ─────────────────────────────────────────────────────────────────
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ─── Node.js 20 (para Vite y el servicio vite) ────────────────────────────────
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www

# ─── Entrypoint ───────────────────────────────────────────────────────────────
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
