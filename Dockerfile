FROM php:8.2-apache

# Apache mod_rewrite
RUN a2enmod rewrite

# PHP uzantıları
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev && \
    docker-php-ext-install pdo pdo_mysql zip

# Çalışma dizini
WORKDIR /var/www/html

# Proje dosyalarını kopyala
COPY . /var/www/html

# Public klasörünü Apache için DocumentRoot yap
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|/var/www/|/var/www/html/public|g' /etc/apache2/apache2.conf

# Storage ve cache için izinler
RUN mkdir -p storage bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

# Composer ekle ve paketleri yükle
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

EXPOSE 80

CMD ["apache2-foreground"]
