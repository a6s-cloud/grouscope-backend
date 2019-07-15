FROM nginx:1.17
MAINTAINER Tsutomu Nakamura<tsuna.0x00@gmail.com>

COPY grouscope.conf 

RUN apt-get update && \
        apt-get upgrade && \
        apt-get install -y --no-install-recommends libfreetype6-dev libjpeg-dev libpng-dev libwebp-dev libgmp-dev
        #docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ --with-png-dir=/usr/inclue/ --with-webp-dir=/usr/include/

COPY default-site.conf /etc/nginx/conf.d/default.conf

# このCMD のコマンドは /usr/local/bin/docker-php-entrypoint に渡される

