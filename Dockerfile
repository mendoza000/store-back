# Usar PHP 8.2 con FPM
FROM php:8.2-fpm

# Argumentos de construcción
ARG user=laravel
ARG uid=1000

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    postgresql-client \
    netcat-openbsd

# Limpiar caché de apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensiones de PHP
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# Obtener la última versión de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Crear usuario del sistema para ejecutar comandos de Composer y Artisan
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Crear directorio de la aplicación
WORKDIR /var/www

# Copiar archivos de dependencias
COPY composer.json composer.lock ./

# Instalar dependencias de Composer (incluyendo dev para evitar errores)
RUN composer install --no-scripts --no-autoloader --prefer-dist

# Copiar el código de la aplicación
COPY --chown=$user:www-data . .

# Completar la instalación de Composer
RUN composer dump-autoload --optimize

# Copiar configuración de Nginx
COPY docker/nginx/default.conf /etc/nginx/sites-available/default

# Copiar configuración de Supervisor
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Crear directorios necesarios y establecer permisos
RUN mkdir -p /var/www/storage/logs \
    /var/www/storage/framework/cache \
    /var/www/storage/framework/sessions \
    /var/www/storage/framework/views \
    /var/www/bootstrap/cache \
    /var/log/nginx \
    /var/log/php-fpm \
    /var/log/supervisor

RUN chown -R $user:www-data /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache \
    && chown -R $user:www-data /var/log/supervisor

# Copiar y configurar el script de inicialización
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Exponer puerto 3001
EXPOSE 3001

# Establecer el punto de entrada
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Comando de inicio
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]