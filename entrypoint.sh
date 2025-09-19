#!/bin/sh
set -e

echo "[entrypoint] waiting for DB..."
i=0
until php bin/console doctrine:query:sql "SELECT 1" >/dev/null 2>&1; do
  i=$((i+1))
  [ "$i" -ge 30 ] && echo "[entrypoint] DB not ready, giving up" && exit 1
  sleep 2
done

echo "[entrypoint] running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration || true

echo "[entrypoint] starting services..."
exec supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
