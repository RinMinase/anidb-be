FROM nginx:alpine

COPY nginx.conf /etc/nginx/

RUN set -x ; \
    addgroup -g 82 -S www-data ; \
    adduser -u 82 -D -S -G www-data www-data && exit 0 ; exit 1

ARG PHP_UPSTREAM_CONTAINER
ARG PHP_UPSTREAM_PORT

RUN touch /var/log/messages

RUN echo "upstream php-upstream { server ${PHP_UPSTREAM_CONTAINER}:${PHP_UPSTREAM_PORT}; }" > /etc/nginx/conf.d/upstream.conf \
    && rm /etc/nginx/conf.d/default.conf

RUN touch /opt/startup.sh \
    && chmod +x /opt/startup.sh \
    && echo "#!/bin/sh" >> /opt/startup.sh \
    && echo "crond -l 2 -b" >> /opt/startup.sh \
    && echo "nginx" >> /opt/startup.sh

RUN sed -i 's/\r//g' /opt/startup.sh
CMD ["/bin/sh", "/opt/startup.sh"]

EXPOSE 80
