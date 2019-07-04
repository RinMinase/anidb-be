ARG LARADOCK_PHP_VERSION

FROM letsdockerize/laradock-workspace:2.4-${LARADOCK_PHP_VERSION}

ARG LARADOCK_PHP_VERSION

ENV DEBIAN_FRONTEND noninteractive

USER root

RUN set -xe; \
    apt-get update -yqq && \
    pecl channel-update pecl.php.net && \
    groupadd -g 1000 laradock && \
    useradd -u 1000 -g laradock -m laradock -G docker_env && \
    usermod -p "*" laradock -s /bin/bash && \
    apt-get install -yqq \
      apt-utils \
      libzip-dev \
      zip \
      unzip \
      php${LARADOCK_PHP_VERSION}-zip \
      nasm && \
      php -m | grep -q 'zip'

RUN ln -snf /usr/share/zoneinfo/UTC /etc/localtime && echo UTC > /etc/timezone

###########################################################################
# Composer:
###########################################################################

USER root

COPY ./composer.json /home/laradock/.composer/composer.json

RUN chown -R laradock:laradock /home/laradock/.composer

USER laradock

RUN composer global install

RUN echo "" >> ~/.bashrc && \
    echo 'export PATH="~/.composer/vendor/bin:$PATH"' >> ~/.bashrc

###########################################################################
# MongoDB:
###########################################################################

USER root

RUN pecl install mongodb && \
    echo "extension=mongodb.so" >> /etc/php/${LARADOCK_PHP_VERSION}/mods-available/mongodb.ini && \
    ln -s /etc/php/${LARADOCK_PHP_VERSION}/mods-available/mongodb.ini /etc/php/${LARADOCK_PHP_VERSION}/cli/conf.d/30-mongodb.ini

###########################################################################
# PHP GRPC EXTENSION
###########################################################################

RUN apt-get install zlib1g-dev && \
    pecl install grpc && \
    echo "extension=grpc.so" >> /etc/php/${LARADOCK_PHP_VERSION}/mods-available/grpc.ini && \
    ln -s /etc/php/${LARADOCK_PHP_VERSION}/mods-available/grpc.ini /etc/php/${LARADOCK_PHP_VERSION}/cli/conf.d/20-grpc.ini \
    && php -m | grep -q 'grpc'


###########################################################################
# Final Touch
###########################################################################

RUN set -xe; php -v | head -n 1 | grep -q "PHP ${LARADOCK_PHP_VERSION}."

USER root

RUN apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* && \
    rm /var/log/lastlog /var/log/faillog

WORKDIR /var/www
