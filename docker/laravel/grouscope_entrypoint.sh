#!/bin/bash

main() {
    set -e

    cd /var/www/html/a6s-cloud

    /opt/wait_until_mysql_started.sh || {
        exit 1
    }


    composer install
    chown -R www-data:www-data .

    source .env
    if [[ APP_ENV != "production" ]]; then
        find . | xargs chmod o+w .
    fi

    exec php-fpm "$@"
}

main "$@"

exit 0
