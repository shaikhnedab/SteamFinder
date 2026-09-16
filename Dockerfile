# ---- dependencies ----
FROM php:8.4-cli-alpine AS vendor

# bcmath + gmp are hard composer platform requirements (syntax/steam-api, xpaw/steamid).
# NOTE: $PHPIZE_DEPS is intentionally unquoted (space-separated package list).
RUN apk add --no-cache $PHPIZE_DEPS git unzip gmp-dev \
 && docker-php-ext-install -j"$(nproc)" bcmath gmp

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /app

# Copy the full source first (composer scripts boot artisan for package discovery)
COPY . .
RUN cp .env.example .env \
 && composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader \
 && php artisan key:generate --no-interaction

# ---- minimal runtime: single process, plain HTTP ----
FROM php:8.4-cli-alpine

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS gmp-dev libzip-dev \
 && docker-php-ext-install -j"$(nproc)" bcmath gmp zip \
 && apk del .build-deps \
 && apk add --no-cache gmp libzip

WORKDIR /var/www/html

COPY --from=vendor /app /var/www/html
RUN chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

EXPOSE 8000

HEALTHCHECK --interval=30s --timeout=5s --start-period=10s \
  CMD wget -qO- http://127.0.0.1:8000/ || exit 1

# NOTE: artisan serve is fine for dev/small deploys. For production traffic
# put host-level nginx in front as a reverse proxy (see README).
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
