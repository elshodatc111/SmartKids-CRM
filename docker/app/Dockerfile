FROM php:8.3-fpm

# System packages
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    cron \
    supervisor

# PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy project
COPY . /var/www

# Permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Supervisor config
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

CMD ["/usr/bin/supervisord"]
