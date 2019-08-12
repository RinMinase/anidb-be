ARG PHP_VERSION

FROM php:${PHP_VERSION}-fpm-alpine

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

###########################################################################
# Firebase Requirements: Requires g++ and autoconf
###########################################################################

# RUN pecl install -o -f grpc \
#     && docker-php-ext-enable grpc

###########################################################################
# MongoDB: Requires openssl-dev, autoconf and make
###########################################################################

RUN pecl install mongodb && \
    docker-php-ext-enable mongodb

###########################################################################
# Composer
###########################################################################

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
	&& php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
	&& php -r "unlink('composer-setup.php');"

RUN echo "export COMPOSER_ALLOW_SUPERUSER=1" > ~/.bashrc

###########################################################################
# Final Setup
###########################################################################

RUN set -xe; php -v | head -n 1 | grep -q "PHP ${LARADOCK_PHP_VERSION}."

COPY ./php-config/laravel.ini /usr/local/etc/php/conf.d
COPY ./php-config/php-fpm.conf /usr/local/etc/php-fpm.d/

RUN rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

CMD ["php-fpm"]
