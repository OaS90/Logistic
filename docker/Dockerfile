# Используем официальный образ PHP с FPM
FROM php:8.2-fpm

# Установим необходимые системные зависимости и PHP-расширения
RUN apt-get update && apt-get install -y \
        nginx \
        curl \
        wget \
        git \
        zip \
        unzip \
        libfreetype6-dev \
        libcap2-bin \
        libonig-dev \
        libpq-dev \
        libzip-dev \
        libxml2-dev \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        pdo_mysql \
        gd \
        zip \
        sockets

COPY ../docker/php.ini /usr/local/etc/php/conf.d/
# Установим Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Установим рабочую директорию
WORKDIR /var/www

# Копируем файлы приложения
COPY .. .
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN set -eux
# Установим зависимости Laravel
RUN composer install --no-interaction --prefer-dist --ignore-platform-reqs

# Настроим права доступа к директориям
RUN chown -R www-data:www-data /var/www/storage \
    && chmod -R 775 /var/www/storage

# Копируем конфигурацию Nginx
COPY ../docker/nginx.conf /etc/nginx/sites-available/default

# Откроем порты
EXPOSE 80

# Запустим Nginx и PHP-FPM
CMD service nginx start && php-fpm
