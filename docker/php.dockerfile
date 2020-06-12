ARG PHP_VERSION

FROM php:${PHP_VERSION}-fpm-alpine

RUN set -xe; \
		apk add --no-cache \
		bash \
		libzip-dev \
		openssl-dev \
		autoconf \
		make \
		g++ \
		libpng-dev

###########################################################################
# PHP Extensions
###########################################################################

RUN docker-php-ext-configure zip --with-libzip && \
		docker-php-ext-install zip

###########################################################################
# MongoDB: Requires openssl-dev, autoconf, make and g++
###########################################################################

RUN pecl install mongodb && \
		docker-php-ext-enable mongodb

###########################################################################
# PHPSpreadsheet: Requires libpng-dev
###########################################################################

RUN docker-php-ext-install gd

###########################################################################
# Composer
###########################################################################

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
	&& php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
	&& php -r "unlink('composer-setup.php');"

RUN echo "export COMPOSER_ALLOW_SUPERUSER=1" > ~/.bashrc

###########################################################################
# Composer Autocomplete
###########################################################################

# RUN curl -#L https://git.io/JfUy0 -o composer-autocomplete \
# 	&& mv ./composer-autocomplete ~/composer-autocomplete

# RUN echo "" >> ~/.bashrc \
# 	&& echo 'if [ -f "$HOME/composer-autocomplete" ] ; then' >> ~/.bashrc \
# 	&& echo '    . $HOME/composer-autocomplete' >> ~/.bashrc \
# 	&& echo "fi" >> ~/.bashrc

###########################################################################
# NodeJS for APIDoc
###########################################################################

RUN set -xe; \
		apk add --no-cache \
		nodejs \
		npm \
		yarn

###########################################################################
# Final Setup
###########################################################################

RUN set -xe; php -v | head -n 1 | grep -q "PHP ${PHP_VERSION}."

COPY ./php-config/laravel.ini /usr/local/etc/php/conf.d
COPY ./php-config/php-fpm.conf /usr/local/etc/php-fpm.d/

RUN rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

CMD ["php-fpm"]
