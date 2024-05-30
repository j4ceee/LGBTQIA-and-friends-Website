# syntax=docker/dockerfile:1

FROM composer:lts as deps
WORKDIR /app
RUN --mount=type=bind,source=composer.json,target=composer.json \
    --mount=type=bind,source=composer.lock,target=composer.lock \
    --mount=type=cache,target=/tmp/cache \
    composer install --no-dev --no-interaction

FROM node:20-alpine as node-builder

# install inotify-tools for file watching
RUN apk add --no-cache inotify-tools

# update npm packages
RUN npm install -g npm@10.8.1 \
    && npm uninstall -g glob@7.2.3 \
    && npm install -g glob@10.4.1 \
    && npm uninstall -g inflight@1.0.6

# install clean-css-cli
RUN npm install -g clean-css-cli@5.6.3

WORKDIR /app

COPY ./node_sh /app/node_sh

FROM php:8.2-apache as final

RUN docker-php-ext-install pdo pdo_mysql
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Copy app files from the app directory.
COPY --from=deps app/vendor/ /var/www/html/vendor
COPY ./src /var/www/html

# Change the ownership of the /var/www/html/cal directory to www-data
RUN chown -R www-data:www-data /var/www/html/cal

# Switch to a non-privileged user (defined in the base image) that the app will run under.
USER www-data
