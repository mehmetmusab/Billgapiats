# PHP 8.2 + Apache
FROM php:8.2-apache

# Gerekli sistem paketleri
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libonig-dev libpq-dev libpng-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Apache mod_rewrite
RUN a2enmod rewrite

# Çalışma dizini
WORKDIR /var/www/html

# Proje dosyalarını kopyala
COPY . /var/www/html

# Composer'ı image'a ekle
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Storage ve cache dizinlerini oluştur, izin ver ve sonra composer install çalıştır
RUN mkdir -p storage bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache \
    && composer install --no-dev --optimize-autoloader

# Uygulama 80 portundan yayın yapacak
EXPOSE 80

# Apache'yi foreground'da çalıştır
CMD ["apache2-foreground"]
