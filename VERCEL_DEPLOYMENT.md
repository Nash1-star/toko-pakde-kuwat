# Deploy Laravel to Vercel

This deployment uses Vercel's container runtime with FrankenPHP and an external
PostgreSQL database. The local SQLite database is intentionally excluded; a new
production database starts empty unless the local data is imported separately.

See Vercel's [Laravel with Docker guide](https://vercel.com/kb/guide/laravel-php-with-docker)
for platform prerequisites and current container-runtime availability.

## 1. Connect the repository

Push the desired code to GitHub, then import the repository in Vercel. Keep the
project root set to the repository root so Vercel can read `vercel.json` and
`Dockerfile.vercel`.

## 2. Create the production database

Create a PostgreSQL database with Neon or another managed provider. Use the
provider's direct (non-pooled) PostgreSQL connection string for `DB_URL`,
especially for the first migration run. Do not commit the URL or paste it into
chat.

Add these environment variables to the Vercel **Production** environment:

| Variable | Value |
| --- | --- |
| `APP_KEY` | Generate locally with `php artisan key:generate --show`, then add the output as a Vercel secret |
| `APP_URL` | The deployed HTTPS URL |
| `ACCOUNT_MANAGEMENT_PASSWORD` | A strong, unique password |
| `DB_URL` | The direct PostgreSQL connection string |
| `RUN_DATABASE_MIGRATIONS` | Set to `true` for the first production deployment only |

`APP_ENV`, `APP_DEBUG`, database/session/cache drivers, secure cookies, and the
temporary storage path are set by the Vercel container image.

## 3. Deploy and initialize

Deploy from Vercel. On the first production start, the container runs
`php artisan migrate --force` when `RUN_DATABASE_MIGRATIONS=true`. After the
database tables have been created, remove that variable or set it to `false`
and redeploy. Do not enable it for Preview deployments.

Create the first operator in the managed database's SQL console after migrations
finish:

```sql
INSERT INTO users (name, pin, role, created_at, updated_at)
VALUES ('Operator', '<choose-a-unique-pin>', 'operator', NOW(), NOW());
```

Do not run the default seeders in production: they contain demo users with the
PIN `1234`. Operator PINs are hashed on save, and legacy plain-text PINs are
upgraded to hashes after a successful login. Login attempts are rate-limited.

## Import existing SQLite data

The Vercel container filesystem is temporary, so the local
`database/database.sqlite` file is never used by the deployed app. To reuse data
from a previous local run, copy records into PostgreSQL after migrations and
before opening the app to operators. Preserve primary keys and insert parent
records first: users and categories, then products and shifts, then transactions,
transaction details, and stock mutations. Do not import the local `migrations`
table; let the production database track its own migrations. Keep the local
SQLite file as a backup until record counts and login have been verified.

## Runtime notes

- Sessions and cache use PostgreSQL so they survive separate Vercel instances.
- Queue processing is synchronous; there is no background worker in this app.
- Runtime files and compiled views use `/tmp` and are not durable. Store any
  future uploads in object storage rather than the container filesystem.
- The Vercel container runtime must be available to the Vercel project/plan.
