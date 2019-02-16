#!/usr/bin/env bash

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

EMOJI_STAR_STRUCK="🤩"

main() {
    cd "$SCRIPT_DIR"
    check_your_environment || return 1
    build || return 1

    echo "構築が完了しました${EMOJI_STAR_STRUCK} 。http://localhost をWeb ブラウザで開いてWeb アプリ画面が表示されることを確認してください。"

    return 0
}

build() (
    set -e

    git submodule update --init --recursive

    cd laradock
    cp env-example .env

    echo ''                         >> .env
    echo 'DB_HOST=mysql'            >> .env
    echo '# REDIS_HOST=redis'       >> .env
    echo 'QUEUE_HOST=beanstalkd'    >> .env

    docker-compose up -d nginx mysql workspace

    sync ; sleep 3

    docker-compose exec workspace runuser -l laradock -c \
            'cd /var/www; if [ ! -d a6s-cloud ]; then composer create-project laravel/laravel a6s-cloud; else echo "NOTICE: Laravel プロジェクトが既に作成されているので処理をスキップします"; fi'
    if [[ ! -f nginx/sites/default.conf.bak ]]; then
        cp nginx/sites/default.conf nginx/sites/default.conf.bak
        cp nginx/sites/laravel.conf.example default.conf
        sed -i -e 's|\(.*root\) .*/var/www/public.*|\1 /var/www/a6s-cloud/public;|g' nginx/sites/default.conf
    fi
    docker-compose stop && docker-compose up -d nginx mysql workspace
)

check_your_environment() {
    command -v docker || {
        echo "ERROR: docker コマンドが見つかりません" >&2
        return 1
    }
    command -v docker-compose || {
        echo "ERROR: docker-compose コマンドが見つかりません" >&2
        return 1
    }

    docker info > /dev/null 2>&1 || {
        echo "ERROR: docker info コマンドに失敗しました。docker デーモンが起動していないか、docker コマンドを実行する権限がないかもしれません" >&2
        return 1
    }

    return 0
}

main "$@" || {
    echo "ERROR: 処理失敗"
    exit 1
}

