FROM php:7.2.0-fpm-alpine
RUN docker-php-ext-install pdo_mysql

RUN apk update && apk --no-cache upgrade
RUN apk add git
RUN docker-php-source extract
RUN git clone -b 4.1.1 --depth 1 https://github.com/phpredis/phpredis.git /usr/src/php/ext/redis
RUN docker-php-ext-install redis
