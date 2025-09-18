FROM php:8.3-fpm-bookworm

RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        git unzip \
        libicu-dev libzip-dev libpq-dev; \
    docker-php-ext-install intl pdo pdo_mysql opcache zip; \
    pecl install redis; docker-php-ext-enable redis; \
    apt-get clean; rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/backend
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory.ini
