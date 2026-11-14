FROM php:8.2-fpm-alpine

# Install system dependencies & PHP extensions
RUN apk add --no-cache \
    nginx \
    curl \
    zip \
    unzip \
    git \
    bash \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    oniguruma-dev \
    libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo_mysql \
    exif \
    pcntl \
    bcmath \
    gd \
    opcache \
    zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# 1. Copy composer definitions first for layer caching
COPY composer.json composer.lock ./

# 2. Install PHP dependencies (production only, cached unless dependencies change)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# 3. Copy application source code
COPY . .

# 4. Generate final autoloader
RUN composer dump-autoload --optimize

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Copy Nginx & PHP config
COPY ./docker/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/local.ini /usr/local/etc/php/conf.d/local.ini

EXPOSE 80

# Run PHP-FPM and Nginx together
CMD php-fpm -D && nginx -g 'daemon off;'
