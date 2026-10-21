# ---- dependencies ----
# Pinned to 8.4.27: the first release with the php2shell fix
# (CVE-2026-109618 / CVE-2026-109619, 2026-10-21). The floating 8.4-cli-alpine
# alias can lag a release by hours, so the pin pairs with the CI assertion
# (minimum 8.4.27). Bump both FROM lines together on 8.4.28+.
FROM php:8.4.27-cli-alpine AS vendor

# gmp is a hard platform requirement of xpaw/steamid, which does the SteamID
# <-> SteamID3/SteamID64 conversions. Nothing else needs a math extension.
# NOTE: $PHPIZE_DEPS is intentionally unquoted (space-separated package list).
RUN apk add --no-cache $PHPIZE_DEPS git unzip gmp-dev \
 && docker-php-ext-install -j"$(nproc)" gmp

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /app

# Copy the full source first (composer scripts boot artisan for package discovery)
COPY . .
RUN cp .env.example .env \
 && composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader \
 && php artisan key:generate --no-interaction

# There is no database, so there is nothing to migrate.

# ---- minimal runtime: single process, plain HTTP ----
FROM php:8.4.27-cli-alpine

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS gmp-dev libzip-dev \
 && docker-php-ext-install -j"$(nproc)" gmp zip \
 && apk del .build-deps \
 && apk add --no-cache gmp libzip

WORKDIR /var/www/html

COPY --from=vendor /app /var/www/html
RUN chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

# php2shell hardening (CVE-2026-109618/109619, fixed in PHP 8.4.27+): ships the
# advisory's mitigation values — memory_limit=-1 (boundary enforced by the
# compose mem_limit), uploads off, post_max_size=8M,
# session.upload_progress.enabled=0, fatal_error_backtraces=0.
COPY docker/php.ini /usr/local/etc/php/php.ini

EXPOSE 8000

HEALTHCHECK --interval=30s --timeout=5s --start-period=10s \
  CMD wget -qO- http://127.0.0.1:8000/ || exit 1

# `artisan serve` wraps PHP's built-in server, which is single-threaded by
# default: one slow Steam API call blocks every other request (assets
# included). Fork a small pool of workers so a slow lookup cannot stall the
# rest of the page.
ENV PHP_CLI_SERVER_WORKERS=8

# Run unprivileged. storage/ and bootstrap/cache are already chowned above.
USER www-data

# NOTE: artisan serve is fine for dev/small deploys. For production traffic
# put host-level nginx in front as a reverse proxy (see README).
# --no-reload is required: Laravel 13 refuses to honour PHP_CLI_SERVER_WORKERS
# (and silently runs a single server) when the auto-reload watcher is on.
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000", "--no-reload"]
