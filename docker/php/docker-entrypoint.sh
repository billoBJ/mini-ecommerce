#!/bin/sh
set -e

cd /var/www/html

mkdir -p \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

if [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
fi

if [ ! -d vendor ]; then
    composer install --prefer-dist --no-interaction --no-progress
fi

chown -R www-data:www-data storage bootstrap/cache || true
chmod -R ug+rwx storage bootstrap/cache || true

if [ -n "${DB_HOST:-}" ]; then
    echo "Waiting for PostgreSQL at ${DB_HOST}:${DB_PORT:-5432}..."
    until pg_isready -h "$DB_HOST" -p "${DB_PORT:-5432}" -U "${DB_USERNAME:-root}" -d "${DB_DATABASE:-mini_ecommerce}" -q; do
        sleep 1
    done
fi

if [ -z "${APP_KEY:-}" ] || [ "$APP_KEY" = "base64:" ]; then
    php artisan key:generate --no-interaction --force
fi

php artisan package:discover --ansi --no-interaction >/dev/null 2>&1 || true
php artisan storage:link --no-interaction >/dev/null 2>&1 || true

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    php artisan migrate --force --no-interaction
fi

touch /tmp/app-ready

exec "$@"
