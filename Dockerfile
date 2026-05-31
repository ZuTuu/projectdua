FROM php:8.2-cli

# Install dependencies yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo_mysql gd zip

# Set working directory
WORKDIR /var/www/html

# Copy semua file project
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Pastikan permission folder storage dan cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port (Render menggunakan port dinamis, tapi kita buka 8080 sebagai default)
EXPOSE 8080

# Start the server (Jalankan optimize:clear dan serve bersamaan saat runtime menggunakan shell)
CMD sh -c "php artisan optimize:clear && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"