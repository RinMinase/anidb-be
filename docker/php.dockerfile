ARG LARADOCK_PHP_VERSION

FROM letsdockerize/laradock-php-fpm:2.4-${LARADOCK_PHP_VERSION}

ENV DEBIAN_FRONTEND noninteractive

RUN set -xe; \
    apt-get update -yqq && \
    pecl channel-update pecl.php.net && \
    apt-get install -yqq \
      apt-utils \
      libzip-dev \
      zip \
      unzip && \
      docker-php-ext-configure zip --with-libzip && \
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
RUN apt-get install -y zlib1g-dev libicu-dev g++ && \
    docker-php-ext-configure intl && \
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

RUN apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* && \
    rm /var/log/lastlog /var/log/faillog

RUN usermod -u 1000 www-data

WORKDIR /var/www

CMD ["php-fpm"]

EXPOSE 9000
