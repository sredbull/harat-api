FROM alpine:3.7

LABEL maintainer="Sven Roodbol <roodbol.sven@housearatus.space>"

ADD https://php.codecasts.rocks/php-alpine.rsa.pub /etc/apk/keys/php-alpine.rsa.pub
RUN apk --update add ca-certificates
RUN echo "@php https://php.codecasts.rocks/v3.7/php-7.2" >> /etc/apk/repositories

RUN apk add --update \
    curl \
    make \
    php@php \
    php-apcu@php \
    php-ctype@php \
    php-curl@php \
    php-dom@php \
    php-fpm@php \
    php-gd@php \
    php-iconv@php \
    php-imagick@php \
    php-intl@php \
    php-json@php \
    php-ldap@php \
    php-mbstring@php \
    php-mysqli@php \
    php-opcache@php \
    php-openssl@php \
    php-pdo@php \
    php-pdo_mysql@php \
    php-phar@php \
    php-session@php \
    php-xdebug@php \
    php-xml@php \
    php-zlib@php

RUN rm -rf /var/cache/apk/* && rm -rf /tmp/*

RUN curl --insecure https://getcomposer.org/composer.phar -o /usr/bin/composer && chmod +x /usr/bin/composer

ADD symfony.ini /etc/php7/php-fpm.d/
ADD symfony.ini /etc/php7/cli/conf.d/
ADD xdebug.ini  /etc/php7/conf.d/

ADD symfony.pool.conf /etc/php7/php-fpm.d/

CMD ["php-fpm7", "-F"]

WORKDIR /var/www/symfony
EXPOSE 9000
EXPOSE 9001