#!/bin/bash

main() {
    set -e

    chown -R www-data:www-data /var/www/html
    su www-data -c '
        set -e
        cd /var/www/html/a6s-cloud

        composer install

        if [[ ! -f .env ]]; then
            cp -ip .env.example .env
            php artisan key:generate
        fi
        [[ ! -L ./public/storage ]] && php artisan storage:link
        php artisan migrate
        php artisan db:seed
    '

    set -- php-fpm "$@"
    exec "$@"
}
main "$@"

exit 0
