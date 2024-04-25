ARG PHP_VERSION

FROM php:${PHP_VERSION}-fpm-alpine3.17

RUN set -xe; \
    apk add --no-cache \
    bash \
    libpng-dev \
    postgresql-dev

###########################################################################
# PHPSpreadsheet: Requires libpng-dev
###########################################################################

RUN docker-php-ext-install gd

###########################################################################
# PostgreSQL: Requires postgresql-dev
###########################################################################

RUN docker-php-ext-install pdo pdo_pgsql

###########################################################################
# Composer
###########################################################################

ARG COMPOSER_VERSION

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
  && php composer-setup.php --version=${COMPOSER_VERSION:-2.7.4} \
  && php -r "unlink('composer-setup.php');"

RUN mv composer.phar /usr/local/bin/composer

###########################################################################
# Setting up shell profile
###########################################################################

ENV ENV="/root/.ashrc"

RUN echo "alias pa='php artisan'" >> "$ENV" \
  && echo "alias artisan='php artisan'" >> "$ENV" \
  && echo "alias la='ls -la'" >> "$ENV" \
  && echo "alias da='composer dumpautoload'" >> "$ENV" \
  && echo "alias dump='composer dumpautoload'" >> "$ENV" \
  && echo "alias doc='composer docs'" >> "$ENV" \
  && echo "alias docs='composer docs'" >> "$ENV"

###########################################################################
# Final Setup
###########################################################################

RUN set -xe; php -v | head -n 1 | grep -q "PHP ${PHP_VERSION}."

COPY ./php-config/laravel.ini /usr/local/etc/php/conf.d
COPY ./php-config/php-fpm.conf /usr/local/etc/php-fpm.d/

RUN rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

CMD ["php-fpm"]
