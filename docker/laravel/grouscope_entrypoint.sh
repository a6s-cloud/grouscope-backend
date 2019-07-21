#!/bin/bash

main() {
    set -e

    cd /var/www/html/a6s-cloud
    composer install
    chown -R www-data:www-data /var/www/html

    exec php-fpm "$@"
}

main "$@"

exit 0
