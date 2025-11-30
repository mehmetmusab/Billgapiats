FROM php:8.2-apache

# Enable Apache mods
RUN a2enmod rewrite
RUN service apache2 restart

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

# Copy project
COPY . .

# Give permissions for Laravel
RUN mkdir -p storage/framework/{sessions,views,cache,data} \
    && mkdir -p bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Optimize Laravel
RUN php artisan config:clear && php artisan cache:clear && php artisan route:clear && php artisan view:clear
RUN php artisan config:cache && php artisan route:cache && php artisan view:cache

# Expose port
EXPOSE 80

CMD ["apache2-foreground"]
