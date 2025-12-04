# Usamos PHP con Apache (igual que XAMPP)
FROM php:8.2-apache

# Instalamos extensiones del sistema necesarias para Composer y Zip
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev

# Instalamos extensiones de PHP requeridas (PDO MySQL es vital para tu proyecto)
RUN docker-php-ext-install pdo pdo_mysql zip

# Habilitamos mod_rewrite de Apache (Necesario para rutas amigables/MVC)
RUN a2enmod rewrite

# Instalamos Composer (Para Transbank SDK y PHPMailer)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configuramos el directorio de trabajo
WORKDIR /var/www/html

# Copiamos archivos de dependencias
COPY composer.json composer.lock ./

# Instalamos dependencias de PHP (Optimizado para producción)
RUN composer install --no-dev --optimize-autoloader

# Copiamos el resto del código
COPY . .

# Ajustamos permisos para Apache
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

# Exponemos el puerto 80
EXPOSE 80