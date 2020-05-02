FROM openfaas/of-watchdog:0.7.2 as watchdog

# start with the official Composer image and name it
FROM composer:1.7 AS composer

# continue with the official PHP image
FROM php:7.4-alpine

# copy the Composer PHAR from the Composer image into the PHP image
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Composer requirements
ARG COMPOSER_AUTH='{}'
ENV COMPOSER_AUTH=${COMPOSER_AUTH}

COPY --from=watchdog /fwatchdog /usr/bin/fwatchdog
RUN chmod +x /usr/bin/fwatchdog

RUN apk add --no-cache git

# create non-root user
RUN addgroup -S app && adduser -S -g app app && \
    mkdir -p /home/app

# Import function
WORKDIR /home/app
COPY index.php ./
COPY ./function ./function
WORKDIR /home/app/function

# Install php extensions
RUN [[ -f php-extension.sh ]] && sh ./php-extension.sh && \
    chown -R app /home/app

# Entry
USER app

# Install Composer Dependecies
RUN [[ -f composer.lock || -f composer.json ]] && composer install --no-dev

USER root

# Cleanup
RUN apk del git && \
    rm -rf /usr/src/php && \
    { find /usr/local/lib -type f -print0 | xargs -0r strip --strip-all -p 2>/dev/null || true; }

USER app

WORKDIR /home/app

ENV fprocess="php -S 127.0.0.1:3000 index.php"
ENV cgi_headers="true"
ENV mode="http"
ENV upstream_url="http://127.0.0.1:3000"

ENV exec_timeout="10s"
ENV write_timeout="15s"
ENV read_timeout="15s"

HEALTHCHECK --interval=3s CMD [ -e /tmp/.lock ] || exit 1
CMD ["fwatchdog"]
