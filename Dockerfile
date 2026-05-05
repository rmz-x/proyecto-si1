FROM php:8.2-apache

# Instalar dependencias del sistema + PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Copiar el proyecto al contenedor
COPY . /var/www/html/

# Permisos correctos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Puerto (Render usa 80 por defecto en contenedores web)
EXPOSE 80