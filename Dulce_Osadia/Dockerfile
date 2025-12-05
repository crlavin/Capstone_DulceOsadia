# Usamos PHP 8.2 con Apache
FROM php:8.2-apache

# 1. Dependencias del sistema
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libssl-dev libcurl4-openssl-dev pkg-config libxml2-dev

# 2. Extensión MongoDB
RUN pecl install mongodb && docker-php-ext-enable mongodb

# 3. Extensiones PHP
RUN docker-php-ext-install pdo pdo_mysql zip soap curl

# Mod rewrite
RUN a2enmod rewrite

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html

# Copiamos dependencias y código
COPY composer.json ./
# IMPORTANTE: Usamos update e ignoramos plataforma para evitar errores de bloqueo
RUN composer update --no-dev --optimize-autoloader --ignore-platform-reqs

COPY . .

# --- SECCIÓN NUEVA: CAMBIAR LA CARPETA PÚBLICA DE APACHE ---
# Definimos que la carpeta pública real es /php
ENV APACHE_DOCUMENT_ROOT /var/www/html/php

# Reemplazamos la configuración interna de Apache automáticamente
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
# -----------------------------------------------------------

# Permisos
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

EXPOSE 80