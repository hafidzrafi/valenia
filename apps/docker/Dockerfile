FROM php:8.5.12-fpm-alpine3.24

RUN apk add --no-cache postgresql-dev && docker-php-ext-install pdo_pgsql

COPY --from=composer:2.10.3 /usr/bin/composer /usr/bin/composer

WORKDIR /srv

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-autoloader --no-scripts

COPY . .

RUN composer dump-autoload --optimize --no-dev