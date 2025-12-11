FROM php:8.2-apache

# Install Extension yang dibutuhkan
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Aktifkan mod_rewrite untuk routing index.php
RUN a2enmod rewrite

# Ganti DocumentRoot ke folder /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf | sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/conf-available/*.conf

# Set Working Directory
WORKDIR /var/www/html

# Copy semua source code
COPY . /var/www/html/

# Berikan hak akses ke www-data
RUN chown -R www-data:www-data /var/www/html
