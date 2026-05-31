FROM php:8.2-apache

# Install ekstensi PHP yang dibutuhkan
RUN apt-get update && apt-get install -y libpng-dev libzip-dev zip unzip \
    && docker-php-ext-install pdo_mysql gd zip

# Set working directory
WORKDIR /var/www/html

# Copy file sertifikat ke dalam container
COPY cert.pem /var/www/html/cert.pem
# Copy file project
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Setup Permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Ubah Document Root Apache ke folder public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

EXPOSE 80