FROM php:7.3-fpm-stretch
MAINTAINER Tsutomu Nakamura<tsuna.0x00@gmail.com>

RUN apt-get update && \
        DEBIAN_FRONTEND=noninteractive apt-get upgrade -y && \
        DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends libfreetype6-dev libjpeg-dev libpng-dev libwebp-dev libgmp-dev libzip-dev zip unzip && \
        docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ --with-png-dir=/usr/inclue/ --with-webp-dir=/usr/include/ && \
        docker-php-ext-configure zip --with-libzip && \
        docker-php-ext-install gd gmp pdo_mysql zip && \
        docker-php-ext-enable opcache && \
        # Finally clean cache and remove its dir
        apt-get autoclean -y && \
        rm -rf /var/lib/apt/lists/* /tmp/pear/ && \
        pecl channel-update pecl.php.net && \
        apt-get install -yqq apt-utils && \
        # xdebug
        pecl install xdebug && \
        docker-php-ext-enable xdebug && \
        { \
            echo "; xdebug.remote_host=dockerhost"; \
            echo "xdebug.remote_connect_back=1"; \
            echo "xdebug.remote_port=9000"; \
            echo "xdebug.idekey=PHPSTORM"; \
            echo ""; \
            echo "xdebug.remote_autostart=0"; \
            echo "xdebug.remote_enable=0"; \
            echo "xdebug.cli_color=0"; \
            echo "xdebug.profiler_enable=0"; \
            echo "xdebug.profiler_output_dir=\"~/xdebug/phpstorm/tmp/profiling\""; \
            echo ""; \
            echo "xdebug.remote_handler=dbgp"; \
            echo "xdebug.remote_mode=req"; \
            echo ""; \
            echo "xdebug.var_display_max_children=-1"; \
            echo "xdebug.var_display_max_data=-1"; \
            echo "xdebug.var_display_max_depth=-1"; \
        } > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# pecl channel-update pecl.php.net
# apt-get install -yqq apt-utils
## apt-get install -yqq libzip-dev
## apt-get install -yqq zip
## apt-get install -yqq unzip
## docker-php-ext-configure zip --with-libzip
## docker-php-ext-install zip
# pecl install xdebug
# docker-php-ext-enable xdebug

