
main() {
    cd /var/www/html
    composer install
    if [[ ! -f .env ]]; then
        cp -ip .env.example .env
        php artisan key:generate
    fi

    # TODO:

    true
}

main || exit 1

exit 0

