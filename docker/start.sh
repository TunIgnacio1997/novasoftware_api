#!/bin/sh
set -e

# Permisos de storage (arregla el "Permission denied" del log)
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Migraciones
php artisan migrate --force

# Arranque del servidor (ajusta según tu Dockerfile actual)
exec apache2-foreground
