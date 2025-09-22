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

# Esperar a que la base de datos esté lista usando netcat o timeout
echo "Esperando a que PostgreSQL esté listo..."
timeout=30
counter=0
until nc -z postgres 5432 2>/dev/null || [ $counter -eq $timeout ]; do
  echo "PostgreSQL no está listo - esperando... ($counter/$timeout)"
  sleep 2
  counter=$((counter + 1))
done

if [ $counter -eq $timeout ]; then
  echo "Timeout esperando PostgreSQL, continuando de todas formas..."
else
  echo "PostgreSQL está listo!"
fi

# Limpiar cachés de Laravel
echo "Limpiando cachés de Laravel..."
php artisan config:clear
php artisan view:clear

# Ejecutar migraciones automáticamente
echo "Ejecutando migraciones..."
php artisan migrate --force

# Crear tabla de sessions si no existe
echo "Verificando tabla de sessions..."
php artisan session:table --force 2>/dev/null || echo "Tabla de sessions ya existe o no es necesaria"
php artisan migrate --force

# Generar documentación de Swagger/OpenAPI
echo "Generando documentación de API..."
if php artisan list | grep -q "l5-swagger:generate"; then
    php artisan l5-swagger:generate
    echo "Documentación Swagger generada"
elif php artisan list | grep -q "scribe:generate"; then
    php artisan scribe:generate
    echo "Documentación Scribe generada"  
elif php artisan list | grep -q "api:generate"; then
    php artisan api:generate
    echo "Documentación de API generada"
else
    echo "No se encontró comando de generación de documentación"
fi

# Optimizar para producción
if [ "$APP_ENV" = "production" ]; then
    echo "Optimizando para producción..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

echo "Iniciando aplicación Laravel..."

# Ejecutar el comando pasado como argumento
exec "$@"