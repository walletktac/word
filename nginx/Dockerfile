FROM nginx:1.27-alpine
WORKDIR /var/www/backend
COPY backend/public ./public
COPY .docker/nginx/default.conf.template /etc/nginx/templates/
CMD sh -c 'envsubst "$$PORT $$PHP_FPM_HOST $$PHP_FPM_PORT" \
  < /etc/nginx/templates/default.conf.template \
  > /etc/nginx/conf.d/default.conf && nginx -g "daemon off;"'
