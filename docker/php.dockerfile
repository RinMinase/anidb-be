ARG LARADOCK_PHP_VERSION

FROM php:${LARADOCK_PHP_VERSION}-fpm-alpine

RUN set -xe; \
    apk add --no-cache \
    bash \
    libzip-dev \
    openssl-dev \
    autoconf \
    make \
    g++

###########################################################################
# PHP Extensions
###########################################################################

RUN docker-php-ext-configure zip --with-libzip && \
    docker-php-ext-install zip

RUN docker-php-ext-install bcmath

# RUN docker-php-ext-install opcache

# COPY ./php-config/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

###########################################################################
# Firebase Requirements: Requires g++ and autoconf
###########################################################################

RUN pecl install -o -f grpc \
    && docker-php-ext-enable grpc

###########################################################################
# MongoDB: Requires openssl-dev, autoconf and make
###########################################################################

RUN pecl install mongodb && \
    docker-php-ext-enable mongodb

###########################################################################
# Composer:
###########################################################################

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
	&& php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
	&& php -r "unlink('composer-setup.php');"

###########################################################################
# Verify and Copy PHP Settings
###########################################################################

RUN set -xe; php -v | head -n 1 | grep -q "PHP ${LARADOCK_PHP_VERSION}."

COPY ./php-config/laravel.ini /usr/local/etc/php/conf.d
COPY ./php-config/php-fpm.conf /usr/local/etc/php-fpm.d/

###########################################################################
# Final Touches
###########################################################################

RUN rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

WORKDIR /var/www

CMD ["php-fpm"]

EXPOSE 9000
