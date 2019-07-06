ARG LARADOCK_PHP_VERSION

FROM php:${LARADOCK_PHP_VERSION}-fpm-alpine

RUN set -xe; \
    apk add --no-cache \
    openssl-dev \
    icu-dev \
    libzip-dev \
    autoconf \
    make \
    g++

# icu-dev and g++ are required by php-ext intl
# openssl-dev are required by php-mongo

RUN docker-php-ext-configure zip --with-libzip && \
    docker-php-ext-install zip && \
    php -m | grep -q 'zip'

###########################################################################
# PHP Extensions
###########################################################################

RUN printf "\n" | pecl install -o -f grpc \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable grpc

RUN docker-php-ext-install bcmath

RUN docker-php-ext-install opcache

COPY ./php-config/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Human Language and Character Encoding Support:
RUN docker-php-ext-configure intl && \
    docker-php-ext-install intl

###########################################################################
# MongoDB:
###########################################################################

RUN pecl install mongodb && \
    docker-php-ext-enable mongodb

###########################################################################
# Final Touch
###########################################################################

RUN set -xe; php -v | head -n 1 | grep -q "PHP ${LARADOCK_PHP_VERSION}."

COPY ./php-config/laravel.ini /usr/local/etc/php/conf.d
COPY ./php-config/php-fpm.conf /usr/local/etc/php-fpm.d/

USER root

RUN rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

WORKDIR /var/www

CMD ["php-fpm"]

EXPOSE 9000
