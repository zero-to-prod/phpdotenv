FROM php:7.2-alpine AS builder

RUN apk add --no-cache \
    git \
    unzip \
    $PHPIZE_DEPS

RUN git config --global --add safe.directory /app

WORKDIR /app

FROM builder AS composer

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

FROM builder AS debug

RUN apk add --no-cache --virtual .phpize-deps $PHPIZE_DEPS \
    && pecl channel-update pecl.php.net \
    && pecl install xdebug-2.9.8 \
    && docker-php-ext-enable xdebug \
    && apk del .phpize-deps

FROM php:7.2-alpine AS base

WORKDIR /app

CMD ["bash"]