FROM nginx:alpine

COPY nginx.conf /etc/nginx/

RUN apk update \
    && apk upgrade \
    && apk add --no-cache openssl \
    && apk add --no-cache bash

RUN set -x ; \
    addgroup -g 82 -S www-data ; \
    adduser -u 82 -D -S -G www-data www-data && exit 0 ; exit 1

ARG PHP_UPSTREAM_CONTAINER=php-fpm
ARG PHP_UPSTREAM_PORT=9000

RUN touch /var/log/messages

RUN echo "upstream php-upstream { server ${PHP_UPSTREAM_CONTAINER}:${PHP_UPSTREAM_PORT}; }" > /etc/nginx/conf.d/upstream.conf \
    && rm /etc/nginx/conf.d/default.conf

RUN touch /opt/startup.sh \
    && echo "#!/bin/bash" >> /opt/startup.sh \
    && echo "if [ ! -f /etc/nginx/ssl/default.crt ]; then" >> /opt/startup.sh \
    && echo "openssl genrsa -out '/etc/nginx/ssl/default.key' 2048" >> /opt/startup.sh \
    && echo "openssl req -new -key '/etc/nginx/ssl/default.key' -out '/etc/nginx/ssl/default.csr' -subj '/CN=default/O=default/C=UK'" >> /opt/startup.sh \
    && echo "openssl x509 -req -days 365 -in '/etc/nginx/ssl/default.csr' -signkey '/etc/nginx/ssl/default.key' -out '/etc/nginx/ssl/default.crt'" >> /opt/startup.sh \
    && echo "fi" >> /opt/startup.sh \
    && echo "crond -l 2 -b" >> /opt/startup.sh \
    && echo "nginx" >> /opt/startup.sh

RUN sed -i 's/\r//g' /opt/startup.sh
CMD ["/bin/bash", "/opt/startup.sh"]

EXPOSE 80 443
