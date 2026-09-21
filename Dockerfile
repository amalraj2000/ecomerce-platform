FROM php:8.4-fpm-alpine

# Install system dependencies & PHP extensions
RUN apk add --no-cache \
    nginx \
    supervisor \
    nodejs \
    npm \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    ca-certificates \
    && docker-php-ext-install pdo pdo_mysql bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install PHP dependencies first — Vite needs vendor/tightenco/ziggy to resolve
# the ZiggyVue import in resources/js/app.js during the build step below
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install JS dependencies and build Vue/Tailwind assets for production
RUN npm ci --ignore-scripts && npm run build


# Set correct storage & cache permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy Nginx & Supervisor configuration
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

# Run migrations, create storage symlink, then start Supervisor (which manages Nginx and PHP-FPM)
CMD ["/bin/sh", "-c", "php artisan migrate --force && php artisan storage:link --force && /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf"]
