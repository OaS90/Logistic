# Используем официальный образ PHP с FPM
FROM php:8.2-fpm

ARG GITLAB_PORT=22
ARG GITLAB_HOST=gitlab.adeal.ru
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

RUN git clone --depth 1 https://github.com/edenhill/librdkafka.git --branch v1.2.1 --single-branch \
    && (cd librdkafka && ./configure && make && make install) \
    && pecl install rdkafka \
    && (cd .. && rm -rf librdkafka);
COPY ../docker/rdkafka.ini /usr/local/etc/php/conf.d/rdkafka.ini

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
COPY ../docker/ssh /home/git/.ssh

#RUN if ! test -f /home/git/.ssh/dev_key; then \
#            echo "ssh/config/dev_key not exists."; \
#            exit 255; \
#    fi \

RUN chown -R www-data:www-data /home/git/.ssh
RUN chmod 755 /home/git/.ssh
RUN chmod -R 600 /home/git/.ssh/*
RUN sed -i -e 's/\r$//' /home/git/.ssh/dev_key
RUN sed -i s/{HOST}/$GITLAB_HOST/ /home/git/.ssh/config
RUN sed -i s/{PORT}/$GITLAB_PORT/ /home/git/.ssh/config
RUN rm -rf /var/www/public/storage
RUN php artisan storage:link
# Откроем порты
EXPOSE 80

# Запустим Nginx и PHP-FPM
CMD service nginx start && php-fpm
