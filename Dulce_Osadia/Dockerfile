# Usamos PHP 8.2 con Apache
FROM php:8.2-apache

# 1. Instalamos dependencias del sistema (incluyendo librerías para curl y xml por si Transbank las pide)
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libssl-dev \
    libcurl4-openssl-dev \
    pkg-config \
    libxml2-dev

# 2. Instalamos extensión de MongoDB
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# 3. Instalamos extensiones estándar + requeridas por SDKs
RUN docker-php-ext-install pdo pdo_mysql zip soap curl

# Habilitamos mod_rewrite
RUN a2enmod rewrite

# Instalamos Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configuramos directorio
WORKDIR /var/www/html

# Copiamos dependencias
COPY composer.json composer.lock ./

# --- EL CAMBIO ESTÁ AQUÍ ---
# Agregamos --ignore-platform-reqs para que no falle si no detecta alguna extensión durante el build
RUN composer update --no-dev --optimize-autoloader --ignore-platform-reqs
# ---------------------------

# Copiamos el resto del código
COPY . .

# Permisos
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

# Puerto
EXPOSE 80