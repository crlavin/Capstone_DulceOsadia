# Usamos PHP con Apache
FROM php:8.2-apache

# 1. Instalamos dependencias del sistema (incluyendo librerías SSL para Mongo)
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libssl-dev \
    pkg-config

# 2. Instalamos la extensión de MONGODB (Vital para Atlas)
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# 3. Instalamos extensiones estándar (Zip y MySQL por si acaso quedó algo antiguo)
RUN docker-php-ext-install pdo pdo_mysql zip

# Habilitamos mod_rewrite
RUN a2enmod rewrite

# Instalamos Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configuramos directorio
WORKDIR /var/www/html

# Copiamos archivos de dependencias
COPY composer.json composer.lock ./

# Instalamos librerías de PHP
RUN composer update --no-dev --optimize-autoloader

# Copiamos el código fuente
COPY . .

# Permisos
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

# Puerto
EXPOSE 80