FROM php:7.3.33-fpm-alpine3.14
RUN docker-php-ext-install pdo pdo_mysql