FROM php:8.3-fpm-bookworm

RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        nginx supervisor git unzip netcat-openbsd \
        default-mysql-client \
        libicu-dev libzip-dev libpq-dev; \
    docker-php-ext-install intl pdo pdo_mysql opcache zip; \
    pecl install redis; docker-php-ext-enable redis; \
    rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory.ini

WORKDIR /var/www/backend

COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-progress --no-interaction --no-scripts

COPY . .

RUN APP_ENV=prod composer dump-autoload --optimize && \
    mkdir -p var/cache var/log && \
    chown -R www-data:www-data var

RUN rm -f /etc/nginx/conf.d/*
COPY nginx/default.conf /etc/nginx/conf.d/default.conf
COPY nginx/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80
CMD ["/entrypoint.sh"]
