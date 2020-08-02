FROM php:7.4-fpm-alpine

RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install xdebug-2.9.0 \
    && docker-php-ext-enable xdebug

COPY ./config/php.ini /usr/local/etc/php/php.ini

ENV PHP_IDE_CONFIG 'serverName=DockerApp'

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
