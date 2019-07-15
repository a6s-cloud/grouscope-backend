FROM php:7.3-fpm-stretch
MAINTAINER Tsutomu Nakamura<tsuna.0x00@gmail.com>

RUN apt-get update && \
        apt-get upgrade && \
        apt-get install -y --no-install-recommends libfreetype6-dev libjpeg-dev libpng-dev libwebp-dev libgmp-dev libzip-dev && \
        docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ --with-png-dir=/usr/inclue/ --with-webp-dir=/usr/include/ && \
        docker-php-ext-install gd gmp pdo_mysql zip && \
        docker-php-ext-enable opcache && \
        apt-get autoclean -y && \
        rm -rf /var/lib/apt/lists/* /tmp/pear/

