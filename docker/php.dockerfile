ARG PHP_VERSION

FROM php:${PHP_VERSION}-fpm-alpine3.13

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

# RUN pecl install mongodb && \
# 		docker-php-ext-enable mongodb

###########################################################################
# PHPSpreadsheet: Requires libpng-dev
###########################################################################

RUN docker-php-ext-install gd

###########################################################################
# Composer
###########################################################################

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
	&& php -r "if (hash_file('sha384', 'composer-setup.php') === '55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
	&& php composer-setup.php --version=2.3.5 \
	&& php -r "unlink('composer-setup.php');"

RUN mv composer.phar /usr/local/bin/composer

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
