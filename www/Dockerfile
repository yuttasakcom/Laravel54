FROM php:7.1.8-fpm

MAINTAINER YoProgrammer <yuttasakcom@gmail.com>

RUN apt-get update &&\
  apt-get install -y git php-pear libmcrypt-dev libssl-dev zlib1g-dev \
  libfreetype6-dev \
  libjpeg62-turbo-dev \
  libpng12-dev

RUN pecl install mongodb redis && \
  docker-php-ext-install pdo pdo_mysql && \
  docker-php-ext-install mcrypt && \
  docker-php-ext-enable mongodb && \
  docker-php-ext-enable redis

RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ && \
  docker-php-ext-install -j$(nproc) gd && \
  docker-php-ext-install bcmath

RUN curl -sS https://getcomposer.org/installer | php -- \
  --install-dir=/usr/bin --filename=composer