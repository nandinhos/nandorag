FROM php:8.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libicu-dev \
    libcurl4-gnutls-dev \
    netcat-openbsd \
    poppler-utils \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip gd mbstring exif pcntl bcmath intl \
    && pecl install redis && docker-php-ext-enable redis

# Install Node.js and npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Nginx
RUN apt-get install -y nginx

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install PHP dependencies
RUN git config --global --add safe.directory /var/www/html
RUN mkdir -p bootstrap/cache storage/logs storage/framework/cache storage/framework/sessions storage/framework/views database && chmod -R 775 storage bootstrap/cache database
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Ensure dev packages needed for artisan are available
RUN composer require nunomaduro/collision laravel/pail --no-interaction --dev

# Run Laravel post-install scripts (caches built at runtime after DB is available)
RUN php artisan package:discover --ansi

# Install Node.js dependencies
RUN npm install && npm run build

# Copy Nginx configuration
COPY docker/nginx.conf /etc/nginx/sites-available/default

# Create www.conf for PHP-FPM
RUN echo '[www]' > /usr/local/etc/php-fpm.d/www.conf && \
    echo 'user = www-data' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'group = www-data' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'listen = /var/run/php/php8.4-fpm.sock' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'listen.mode = 0666' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'pm = dynamic' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'pm.max_children = 10' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'pm.start_servers = 2' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'pm.min_spare_servers = 2' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'pm.max_spare_servers = 4' >> /usr/local/etc/php-fpm.d/www.conf

# Expose port
EXPOSE 80

# Create PHP-FPM socket directory and set permissions
RUN mkdir -p /var/run/php && \
    chown -R www-data:www-data /var/run/php && \
    chown -R www-data:www-data /var/www/html && \
    chown -R www-data:www-data /var/log/nginx && \
    chown -R www-data:www-data /var/log/php*.log 2>/dev/null || true

# Fix storage permissions
RUN chown -R www-data:www-data /var/www/html/storage && \
    chmod -R 775 /var/www/html/storage

# Start Nginx + PHP-FPM
CMD ["/bin/sh", "-c", "chown -R www-data:www-data /var/run/php /var/www/html /var/log/nginx && nginx & php-fpm --nodaemonize"]