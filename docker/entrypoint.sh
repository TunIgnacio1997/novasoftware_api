#!/bin/bash

# Enlace simbólico para las imágenes del storage público
php artisan storage:link --force

# Limpiar cachés viejas
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Migraciones (el resultado aparece en los Logs de Render)
php artisan migrate --force || echo ">>> MIGRATE FALLÓ, revisa el error de arriba"

# Permisos DESPUÉS de los comandos artisan (corren como root y pueden crear
# archivos que php-fpm, como www-data, luego no puede escribir)
mkdir -p /var/www/storage/logs
touch /var/www/storage/logs/laravel.log
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# PHP-FPM en background
php-fpm &

# Nginx en primer plano
nginx -g "daemon off;"
