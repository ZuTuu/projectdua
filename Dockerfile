FROM php:8.2-apache

# 1. Install ekstensi PHP, OS dependencies, dan curl
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo_mysql gd zip

# 2. Install Node.js & NPM (Versi 20 LTS)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# 3. Aktifkan modul Rewrite Apache (Wajib untuk Laravel)
RUN a2enmod rewrite

# 4. Arahkan DocumentRoot Apache langsung ke folder /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Aktifkan AllowOverride agar .htaccess Laravel terbaca sempurna
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# 5. Set Working Directory
WORKDIR /var/www/html

# 6. Copy semua file project
COPY . .

# 7. Install dependensi PHP (Composer)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Hapus file public/hot jika tidak sengaja terbawa dari lokal (Penyebab utama Vite 404)
RUN rm -f public/hot

# 8. JALANKAN PROSES BUILD VITE
RUN npm install
RUN npm run build

# 9. BUAT STORAGE LINK (BARU)
# Hapus dulu jika folder public/storage terbawa dari git, lalu buat ulang symlink-nya
RUN rm -rf public/storage && php artisan storage:link

# 10. Set permissions ke seluruh folder /var/www/html
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 11. Buka port 80 untuk Apache
EXPOSE 80