FROM php:8.2-cli

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    && docker-php-ext-install pdo pdo_pgsql zip \
    && apt-get clean

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --no-interaction --optimize-autoloader

# Create storage link and set permissions
RUN php artisan storage:link || true && \
    chmod -R 777 storage bootstrap/cache

EXPOSE 10000

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=60s \
    CMD php -r "echo 'OK';" || exit 1

# Start server - use PORT from Render environment
# Clear all cache, run migrations, then start
CMD php artisan config:clear && \
    php artisan cache:clear && \
    php artisan route:clear && \
    php artisan view:clear && \
    php artisan migrate --force && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
