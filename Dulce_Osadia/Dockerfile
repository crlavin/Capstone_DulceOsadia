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
# Ignoramos plataforma para evitar bloqueos
RUN composer update --no-dev --optimize-autoloader --ignore-platform-reqs

# Copiamos TODO el proyecto
COPY . .

# --- LIMPIEZA DE RUTAS (SOLUCIÓN FINAL) ---
# 1. Movemos el contenido de 'php/' a la raíz principal
RUN cp -r php/* . 2>/dev/null || true

# 2. ¡BORRAMOS la carpeta 'php' original!
# Esto evita que entres por error a /php/index.php y tengas errores de rutas.
RUN rm -rf php
# ------------------------------------------

# Permisos
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

EXPOSE 80