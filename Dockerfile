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

# Set working directory
WORKDIR /var/www/html

# Copy project files into the container
COPY . .

# Give permissions for Laravel
RUN mkdir -p storage/framework/{sessions,views,cache,data} \
    && mkdir -p bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Install PHP dependencies (Laravel vendors)
RUN composer install --no-dev --optimize-autoloader

# BURADA HİÇ artisan KOMUTU YOK
# (config:clear, cache:clear, view:clear, route:cache vs. hepsi kaldırıldı)

# Expose port


CMD ["apache2-foreground"]

