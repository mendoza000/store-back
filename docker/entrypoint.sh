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

# Esperar a que la base de datos esté lista
echo "Esperando a que PostgreSQL esté listo..."
until pg_isready -h postgres -U postgres; do
  echo "PostgreSQL no está listo - esperando..."
  sleep 2
done
echo "PostgreSQL está listo!"

# Limpiar cachés de Laravel (sin usar la base de datos)
php artisan config:clear
# No ejecutar cache:clear si usa database driver sin tablas
php artisan view:clear

# Ejecutar el comando pasado como argumento
exec "$@"