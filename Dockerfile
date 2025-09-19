FROM php:8.3-fpm-bookworm

# --- PHP + Nginx + Supervisor + deps ---
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        nginx supervisor git unzip \
        libicu-dev libzip-dev libpq-dev; \
    docker-php-ext-install intl pdo pdo_mysql opcache zip; \
    pecl install redis; docker-php-ext-enable redis; \
    apt-get clean; rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# PHP config
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory.ini

WORKDIR /var/www/backend

# Instaluj vendory z cache
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-progress --no-interaction

# Kod aplikacji
COPY . .

# (opcjonalnie dla Symfony) niech się nie wywala jeśli brak cache w buildzie
RUN APP_ENV=prod composer dump-autoload --optimize || true

# Nginx conf
COPY nginx/default.conf /etc/nginx/conf.d/default.conf

# Supervisor (odpali Nginx + PHP-FPM)
COPY nginx/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80
CMD ["supervisord","-n","-c","/etc/supervisor/conf.d/supervisord.conf"]
