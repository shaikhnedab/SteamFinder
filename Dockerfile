# ---- dependencies ----
FROM php:8.2-cli-alpine AS vendor

RUN apk add --no-cache git unzip gmp-dev \
 && docker-php-ext-install -j"$(nproc)" bcmath gmp

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /app

# Copy the full source first (composer scripts boot artisan for package discovery)
COPY . .
RUN cp .env.example .env \
 && composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader \
 && php artisan key:generate --no-interaction

# ---- minimal runtime: nginx + php-fpm supervised ----
FROM php:8.2-fpm-alpine

RUN apk add --no-cache --virtual .build-deps "$PHPIZE_DEPS" gmp-dev \
 && docker-php-ext-install -j"$(nproc)" bcmath gmp opcache \
 && apk del .build-deps \
 && apk add --no-cache nginx supervisor gmp

# Let container env vars (STEAM_API_KEY, APP_LANG, ...) reach php-fpm workers
RUN printf '\nclear_env = no\n' >> /usr/local/etc/php-fpm.d/www.conf

COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/opcache.ini /usr/local/etc/php/conf.d/opcache-custom.ini

WORKDIR /var/www/html

COPY --from=vendor /app /var/www/html
RUN mkdir -p /run/nginx /var/log/supervisor \
 && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=15s \
  CMD wget -qO- http://127.0.0.1/ || exit 1

CMD ["supervisord", "-c", "/etc/supervisord.conf"]
