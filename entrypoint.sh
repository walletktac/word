#!/bin/sh
set -e

# Railway daje te zmienne z serwisu PostgreSQL:
#   PGHOST, PGPORT, PGUSER, PGPASSWORD, PGDATABASE
DB_HOST="${PGHOST}"
DB_PORT="${PGPORT:-5432}"
DB_USER="${PGUSER}"
DB_PASS="${PGPASSWORD}"
DB_NAME="${PGDATABASE}"

if [ -z "$DB_HOST" ] || [ -z "$DB_USER" ] || [ -z "$DB_NAME" ]; then
  echo "[entrypoint] Missing PG envs (PGHOST/PGUSER/PGDATABASE). Check Railway Variables."
  exit 1
fi

echo "[entrypoint] waiting for Postgres at ${DB_HOST}:${DB_PORT}..."
i=0
until nc -z "$DB_HOST" "$DB_PORT"; do
  i=$((i+1))
  [ "$i" -ge 90 ] && echo "[entrypoint] Postgres not reachable after 180s. Failing." && exit 1
  sleep 2
done
echo "[entrypoint] DB is reachable."

# Składamy DATABASE_URL dla Doctrine (PDO PgSQL)
export DATABASE_URL="pgsql://${DB_USER}:${DB_PASS}@${DB_HOST}:${DB_PORT}/${DB_NAME}"

echo "[entrypoint] running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration || true

if [ "$APP_ENV" = "dev" ] || [ "$LOAD_FIXTURES" = "1" ]; then
  echo "[entrypoint] loading fixtures..."
  php bin/console doctrine:fixtures:load --no-interaction --group=seed-users || true
  php bin/console doctrine:fixtures:load --no-interaction --group=seed-words || true
fi


echo "[entrypoint] starting services..."
envsubst '$PORT' < /etc/nginx/templates/default.conf.template > /etc/nginx/conf.d/default.conf
exec supervisord -n -c /etc/supervisor/conf.d/supervisord.conf

