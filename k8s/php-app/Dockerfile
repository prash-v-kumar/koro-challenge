FROM php:8.2-apache

# Install required PHP extensions for MySQL and Redis
RUN apt-get update && apt-get install -y \
    && docker-php-ext-install pdo pdo_mysql \
    && pecl install redis \
    && docker-php-ext-enable redis

# Copy the application code
COPY index.php /var/www/html/

# Expose the standard web port
EXPOSE 80