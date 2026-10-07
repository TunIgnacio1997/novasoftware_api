#!/bin/bash

# Generar key si no existe
php artisan key:generate --force

# Crear el enlace simbólico para poder ver las imágenes del storage público
php artisan storage:link --force

# Limpiar cachés viejas para evitar conflictos de rutas y URLs
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Migraciones
php artisan migrate --force

# Iniciar PHP-FPM en background
php-fpm &

# Iniciar Nginx en primer plano
nginx -g "daemon off;"
