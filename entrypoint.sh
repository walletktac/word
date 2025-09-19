#!/bin/sh
set -e

DB_HOST="${MYSQLHOST}"
DB_PORT="${MYSQLPORT:-3306}"
DB_USER="${MYSQLUSER:-root}"
DB_PASS="${MYSQLPASSWORD}"
DB_NAME="${MYSQLDATABASE:-railway}"

echo "[entrypoint] waiting for DB at ${DB_HOST}:${DB_PORT}..."
i=0
until nc -z "$DB_HOST" "$DB_PORT"; do
  i=$((i+1))
  [ "$i" -ge 60 ] && echo "[entrypoint] DB not ready after 120s, giving up" && exit 1
  sleep 2
done
echo "[entrypoint] DB is reachable."

# utwórz usera 'symfony' z hasłem 'symfony' (jeśli nie istnieje)
echo "[entrypoint] ensuring symfony DB user exists..."
mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "
    CREATE USER IF NOT EXISTS 'symfony'@'%' IDENTIFIED WITH mysql_native_password BY 'symfony';
    GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO 'symfony'@'%';
    FLUSH PRIVILEGES;
" || echo "[entrypoint] cannot create symfony user, maybe already exists"


# ustaw nowe DATABASE_URL
export DATABASE_URL="mysql://symfony:symfony@${DB_HOST}:${DB_PORT}/${DB_NAME}?charset=utf8mb4"

echo "[entrypoint] testing doctrine connection..."
if ! php bin/console doctrine:query:sql "SELECT 1" >/dev/null 2>&1; then
  echo "[entrypoint] Doctrine cannot query DB. DATABASE_URL=$DATABASE_URL"
  exit 1
fi

echo "[entrypoint] running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration || true

echo "[entrypoint] starting services..."
exec supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
