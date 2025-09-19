FROM nginx:1.27-alpine
WORKDIR /var/www/backend
COPY backend/public ./public
COPY .docker/nginx/default.conf.template /etc/nginx/templates/

CMD ["nginx", "-g", "daemon off;"]
