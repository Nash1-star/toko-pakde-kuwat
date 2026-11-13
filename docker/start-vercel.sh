#!/bin/sh
set -eu

storage_path="${LARAVEL_STORAGE_PATH:-/tmp/storage}"
mkdir -p \
    "$storage_path/framework/cache/data" \
    "$storage_path/framework/sessions" \
    "$storage_path/framework/views" \
    "$storage_path/logs"

if [ "${VERCEL_RUN_MIGRATIONS:-false}" = "true" ]; then
    php artisan migrate --force
fi

php artisan view:cache
exec frankenphp run --config /etc/frankenphp/Caddyfile
