#!/bin/bash

# Crear directorios si no existen
mkdir -p /var/www/storage/logs
mkdir -p /var/www/storage/framework/cache
mkdir -p /var/www/storage/framework/sessions  
mkdir -p /var/www/storage/framework/views
mkdir -p /var/www/bootstrap/cache

# Establecer permisos correctos
chown -R laravel:www-data /var/www/storage
chown -R laravel:www-data /var/www/bootstrap/cache
chmod -R 775 /var/www/storage
chmod -R 775 /var/www/bootstrap/cache

# Limpiar cachés de Laravel
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Ejecutar el comando pasado como argumento
exec "$@"