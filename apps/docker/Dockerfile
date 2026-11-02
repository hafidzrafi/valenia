# VALENIA application image.
#
# Targets:
#   php-runtime  shared PHP base with pdo_pgsql and no compiler headers left behind
#   builder      Composer dependencies and optimised autoloader
#   dev          local development (Compose bind mounts .:/srv anyway)
#   assets       Tailwind build, discarded afterwards
#   runtime      deployable image: code, vendor, built CSS, non-root user

# ---------------------------------------------------------------- php runtime
FROM php:8.5.12-fpm-alpine3.24 AS php-runtime

# Compile pdo_pgsql with temporary build dependencies, then drop them in the
# same layer so postgresql-dev never ships in the image.
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS postgresql-dev \
    && docker-php-ext-install pdo_pgsql \
    && apk del .build-deps \
    && apk add --no-cache libpq

WORKDIR /srv

# ------------------------------------------------------------------- builder
FROM php-runtime AS builder

COPY --from=composer:2.10.3 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

COPY . .

RUN composer dump-autoload --optimize --classmap-authoritative --no-dev

# ----------------------------------------------------------------------- dev
# Local development target. Compose mounts the working tree over /srv, so this
# exists mainly to keep Composer available for `docker-compose exec app`.
FROM builder AS dev

# -------------------------------------------------------------------- assets
# Tailwind scans the PHP templates through `@source "../../app"` in
# resources/css/app.css, so app/ must be present or utilities are stripped.
FROM node:22-alpine AS assets

WORKDIR /srv

COPY package.json package-lock.json ./
RUN npm ci

COPY resources ./resources
COPY app ./app

RUN npm run css:build

# ------------------------------------------------------------------- runtime
FROM php-runtime AS runtime

RUN adduser -D -u 10001 app \
    && mkdir -p /var/run/php-fpm /usr/local/var/log \
    && chown -R app:app /var/run/php-fpm /usr/local/var/log

COPY --from=builder /srv /srv
COPY --from=assets /srv/public/assets/app.css /srv/public/assets/app.css

RUN chown -R app:app /srv

USER app
