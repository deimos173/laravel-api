FROM php:8.1-cli-alpine

Run apk add --no-cache \
    bash \
    git \
    unzip \
    curl \
    libzip-dev \
    linux-headers \
    $PHPIZE_DEPS \
    && docker-php-ext-install pdo_mysql pcntl \
    && pecl install swoole \
    && docker-php-ext-enable swoole

Copy --from=composer:latest /usr/bin/composer /usr/bin/composer

Workdir /var/www

Copy . .

Run composer install --no-dev --optimize-autoloader

EXPOSE 8080

CMD ["php", "artisan", "octane:start", "--host=0.0.0.0", "--port=8080"]