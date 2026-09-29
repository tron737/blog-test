FROM php:8.4.8-fpm

RUN apt clean
RUN apt-get update

RUN apt-get install -y \
    git zip libicu-dev libzip-dev librabbitmq-dev libssh-dev ffmpeg

RUN docker-php-ext-install \
    intl zip opcache mysqli pdo pdo_mysql sockets bcmath

RUN pecl install xdebug-3.4.0
RUN docker-php-ext-enable xdebug

RUN pecl install amqp
RUN docker-php-ext-enable amqp

RUN pecl install pcov && docker-php-ext-enable pcov

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

USER 1000