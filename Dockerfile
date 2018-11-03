FROM composer:latest as backend
WORKDIR /app

COPY composer.json composer.lock /app/
RUN composer install  \
    --ignore-platform-reqs \
    --no-ansi \
    --no-autoloader \
    --no-dev \
    --no-interaction \
    --no-scripts

COPY . /app/
RUN composer dump-autoload --optimize --classmap-authoritative

FROM richarvey/nginx-php-fpm:latest

ENV WEBROOT=/var/www/html/public/
ENV MEMCACHED_DEPS zlib-dev libmemcached-dev cyrus-sasl-dev git

RUN set -xe \
    && apk add --no-cache libmemcached-libs zlib \
    && apk add --no-cache \
        --virtual .memcached-deps \
        $MEMCACHED_DEPS \
    && git clone -b php7 https://github.com/php-memcached-dev/php-memcached /usr/src/php/ext/memcached \
    && docker-php-ext-configure /usr/src/php/ext/memcached \
        --disable-memcached-sasl \
    && docker-php-ext-install /usr/src/php/ext/memcached \
    && rm -rf /usr/src/php/ext/memcached \
    && apk del .memcached-deps


RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS icu-dev openldap-dev \
    && docker-php-ext-install ldap \
    && docker-php-ext-enable ldap \
    && apk del .build-deps

ADD .env.production /var/www/html/.env

COPY --from=backend /app /var/www/html/
RUN mkdir /var/www/html/var

ARG CACHEBUST=1
RUN chgrp -R nginx /var/www/html/var
RUN chmod -R 777 /var/www/html/var

# RUN php /var/www/html/bin/console doctrine:migrations:migrate --no-interaction
