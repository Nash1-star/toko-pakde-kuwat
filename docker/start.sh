#!/bin/sh
set -e

if [ -z "${DB_HOST:-}" ] && [ -z "${DB_URL:-}" ]; then
	export DB_CONNECTION=sqlite
	export DB_DATABASE=/var/www/html/database/database.sqlite
	export SESSION_DRIVER=file
	export CACHE_STORE=file
	export QUEUE_CONNECTION=sync
	mkdir -p database
	touch "$DB_DATABASE"
fi

php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan migrate --force
php artisan db:seed --class=Database\\Seeders\\UserSeeder --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"