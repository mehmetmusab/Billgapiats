# PHP 8.2 + Apache
FROM php:8.2-apache

# Sistem paketleri
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libonig-dev libpq-dev libpng-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Apache modu
RUN a2enmod rewrite

# Proje dosyalarını kopyala
COPY . /var/www/html

# Çalışma dizini
WORKDIR /var/www/html

# Composer yükle
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Composer install
RUN composer install --no-dev --optimize-autoloader

# Storage izinleri
RUN chmod -R 777 storage bootstrap/cache

# Port aç
EXPOSE 80

# Başlangıç komutu
CMD ["apache2-foreground"]
