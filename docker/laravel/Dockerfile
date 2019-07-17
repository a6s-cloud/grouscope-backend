FROM php:7.3-fpm-stretch
MAINTAINER Tsutomu Nakamura<tsuna.0x00@gmail.com>

RUN apt-get update && \
        DEBIAN_FRONTEND=noninteractive apt-get upgrade -y && \
        DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends libfreetype6-dev libjpeg-dev libpng-dev libwebp-dev libgmp-dev libzip-dev zip unzip mysql-client && \
        docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ --with-png-dir=/usr/inclue/ --with-webp-dir=/usr/include/ && \
        docker-php-ext-configure zip --with-libzip && \
        docker-php-ext-install gd gmp pdo_mysql zip && \
        docker-php-ext-enable opcache && \
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
        } > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
        # Image optimizers
        apt-get install -y jpegoptim optipng pngquant gifsicle && \
        # ImageMagick
        apt-get install -y libmagickwand-dev imagemagick && \
        yes '' | pecl install imagick && \
        docker-php-ext-enable imagick && \
        # Calendar
        docker-php-ext-configure calendar && \
        docker-php-ext-install calendar && \
        # APCU
        yes '' | pecl install apcu && \
        docker-php-ext-enable apcu && \
        # YAML
        apt-get install libyaml-dev -y && \
        yes '' | pecl install yaml && \
        docker-php-ext-enable yaml && \
        # Mysqli
        docker-php-ext-install mysqli && \
        # Finally clean cache and remove its dir
        apt-get autoclean -y && \
        rm -rf /var/lib/apt/lists/* /tmp/pear/ && \
        # composer
        curl -s http://getcomposer.org/installer | php && \
        echo "export PATH=${PATH}:/var/www/vendor/bin" >> ~/.bashrc && \
        mv composer.phar /usr/local/bin/composer
