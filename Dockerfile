FROM php:8.2-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo_mysql zip

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Çalışma klasörü
WORKDIR /var/www/html

# Proje dosyalarını kopyala
COPY . .

# Apache'nin DocumentRoot'unu /public yap
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Laravel için izinler
RUN mkdir -p storage/framework/{sessions,views,cache,data} \
    && mkdir -p bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# PHP bağımlılıklarını yükle
RUN composer install --no-dev --optimize-autoloader

# Port
EXPOSE 80

CMD ["apache2-foreground"]

