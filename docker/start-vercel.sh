#!/bin/sh
set -eu

storage_path="${LARAVEL_STORAGE_PATH:-/tmp/storage}"
mkdir -p \
    "$storage_path/framework/cache/data" \
    "$storage_path/framework/sessions" \
    "$storage_path/framework/views" \
    "$storage_path/logs"

if [ "${RUN_DATABASE_MIGRATIONS:-true}" = "true" ]; then
    php artisan migrate --force || true
fi

php artisan view:cache
exec frankenphp run --config /etc/frankenphp/Caddyfile
