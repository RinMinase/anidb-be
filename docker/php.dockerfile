ARG LARADOCK_PHP_VERSION

FROM php:${LARADOCK_PHP_VERSION}-fpm-alpine

RUN set -xe; \
    apk add --no-cache \
    bash \
    composer \
    libzip-dev \
    openssl-dev \
    autoconf \
    make \
    icu-dev \
    g++

RUN addgroup -g 1000 laradock && \
    adduser -u 1000 -h /home/laradock -G laradock -s /bin/bash -S laradock

# icu-dev and g++ are required by php-ext intl
# openssl-dev autoconf make are required by php-mongo

###########################################################################
# PHP Extensions
###########################################################################

RUN docker-php-ext-configure zip --with-libzip && \
    docker-php-ext-install zip && \
    php -m | grep -q 'zip'

RUN docker-php-ext-install bcmath

RUN docker-php-ext-install opcache

COPY ./php-config/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Human Language and Character Encoding Support:
RUN docker-php-ext-configure intl && \
    docker-php-ext-install intl

###########################################################################
# Firebase Requirements:
###########################################################################

RUN pecl install -o -f grpc \
    && docker-php-ext-enable grpc

###########################################################################
# MongoDB:
###########################################################################

RUN pecl install mongodb && \
    docker-php-ext-enable mongodb

###########################################################################
# Composer:
###########################################################################

USER root

RUN mkdir /home/laradock/.composer \
    && touch /home/laradock/.composer/composer.json \
    && echo '{' >> /home/laradock/.composer/composer.json \
    && echo '    "require": {' >> /home/laradock/.composer/composer.json \
    && echo '' >> /home/laradock/.composer/composer.json \
    && echo '    }' >> /home/laradock/.composer/composer.json \
    && echo '}' >> /home/laradock/.composer/composer.json

RUN chown -R laradock:laradock /home/laradock/.composer

USER laradock

RUN composer global install

RUN echo "" >> ~/.bashrc && \
    echo 'export PATH="~/.composer/vendor/bin:$PATH"' >> ~/.bashrc

###########################################################################
# Final Touch
###########################################################################

USER root

RUN set -xe; php -v | head -n 1 | grep -q "PHP ${LARADOCK_PHP_VERSION}."

COPY ./php-config/laravel.ini /usr/local/etc/php/conf.d
COPY ./php-config/php-fpm.conf /usr/local/etc/php-fpm.d/

RUN rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

WORKDIR /var/www

CMD ["php-fpm"]

EXPOSE 9000
