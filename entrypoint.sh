#!/bin/sh
set -e

# parsowanie hosta i portu z DATABASE_URL
DB_URL="${DATABASE_URL}"
DB_HOST=$(printf "%s" "$DB_URL" | sed -E 's#^.+@([^:/]+).*$#\1#')
DB_PORT=$(printf "%s" "$DB_URL" | sed -nE 's#^.+@[^:/]+:([0-9]+).*$#\1#p')
[ -z "$DB_PORT" ] && DB_PORT=3306

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
mysql -h"$DB_HOST" -P"$DB_PORT" -uroot -p"$MYSQL_ROOT_PASSWORD" railway -e \
  "CREATE USER IF NOT EXISTS 'symfony'@'%' IDENTIFIED BY 'symfony'; \
   GRANT ALL PRIVILEGES ON railway.* TO 'symfony'@'%'; \
   FLUSH PRIVILEGES;" || echo "[entrypoint] cannot create symfony user, maybe already exists"

# nadpisz DATABASE_URL na symfony usera
export DATABASE_URL="mysql://symfony:symfony@${DB_HOST}:${DB_PORT}/railway?charset=utf8mb4"

# testujemy połączenie przez doctrine
if ! php bin/console doctrine:query:sql "SELECT 1" >/dev/null 2>&1; then
  echo "[entrypoint] Doctrine cannot query DB. Check DATABASE_URL: $DATABASE_URL"
  exit 1
fi

echo "[entrypoint] running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration || true

echo "[entrypoint] starting services..."
exec supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
