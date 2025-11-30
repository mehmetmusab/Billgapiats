# Mobile Provider API
This project is a billing system API for a mobile provider.

## Features
- Add subscriber usage
- Calculate bills
- Bill inquiry
- Detailed bill inquiry
- Bill payment

## Technologies
- Laravel 12
- MySQL
- Laravel Sanctum (JWT authentication)
- L5-Swagger (API documentation)

## Data Model

## API Documentation
API documentation can be accessed at:
[https://affectionate-solomon.213-238-168-122.plesk.page/api/documentation]

## Installation
```bash
# Clone the repository
git clone https://github.com/mehmetmusab/mobile-provider-api.git

# Navigate to the project directory
cd mobile-provider-api

# Install dependencies
composer install

# Create .env file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run database migrations
php artisan migrate

# Add sample data
php artisan db:seed

# Add AUTH_GUARD=api to the .env file

# Generate Swagger documentation
php artisan l5-swagger:generate

# Run the application
php artisan serve
