# ==================================
# Stage 1: Builder
# ==================================
FROM php:8.5-apache AS builder

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files (if any in future)
# COPY composer.json composer.lock ./
# RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy application code
COPY . .

# ==================================
# Stage 2: Production
# ==================================
FROM php:8.5-apache

# Install only runtime dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli opcache

# Enable Apache modules
RUN a2enmod rewrite headers expires

# Configure PHP for production
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=10000'; \
    echo 'opcache.revalidate_freq=2'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'opcache.validate_timestamps=0'; \
    echo 'expose_php=Off'; \
    echo 'display_errors=Off'; \
    echo 'log_errors=On'; \
    echo 'error_log=/var/log/php_errors.log'; \
    echo 'max_execution_time=30'; \
    echo 'memory_limit=128M'; \
    echo 'post_max_size=20M'; \
    echo 'upload_max_filesize=10M'; \
} > /usr/local/etc/php/conf.d/production.ini

# Configure Apache DocumentRoot
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Apache security headers
RUN { \
    echo '<IfModule mod_headers.c>'; \
    echo '  Header set X-Content-Type-Options "nosniff"'; \
    echo '  Header set X-Frame-Options "SAMEORIGIN"'; \
    echo '  Header set X-XSS-Protection "1; mode=block"'; \
    echo '  Header set Referrer-Policy "strict-origin-when-cross-origin"'; \
    echo '</IfModule>'; \
} > /etc/apache2/conf-available/security-headers.conf \
    && a2enconf security-headers

# Set working directory
WORKDIR /var/www/html

# Copy application from builder stage
COPY --from=builder --chown=www-data:www-data /var/www/html ./

# Create necessary directories
RUN mkdir -p /var/log && touch /var/log/php_errors.log && chown www-data:www-data /var/log/php_errors.log

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \;

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

# Switch to non-root user
USER www-data

# Expose port
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
