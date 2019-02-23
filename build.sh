#!/usr/bin/bash
set -o errtrace
set -e

# Stack Trace を表示する
# https://gist.github.com/ahendrix/7030300
function errexit() {
  local err=$?
  set +o xtrace
  local code="${1:-1}"
  echo -e "## ${FONT_COLOR_RED}Stack Trace${FONT_COLOR_END} ########################################################"
  echo "Error in ${BASH_SOURCE[1]}:${BASH_LINENO[0]}. '${BASH_COMMAND}' exited with status $err"
  # Print out the stack trace described by $function_stack  
  if [ ${#FUNCNAME[@]} -gt 2 ]
  then
    echo "Call tree:"
    for ((i=1;i<${#FUNCNAME[@]}-1;i++))
    do
      echo " $i: ${BASH_SOURCE[$i+1]}:${BASH_LINENO[$i]} ${FUNCNAME[$i]}(...)"
    done
  fi
  echo "Exiting with status ${code}"
  exit "${code}"
}

# エラー終了時にerrexit を実行する
trap '
    errexit >&2
' ERR

# Color of font red
FONT_COLOR_GREEN='\033[0;32m'
# Color of font yello
FONT_COLOR_YELLOW='\033[0;33m'
# Color of font red
FONT_COLOR_RED='\033[0;31m'
# Color of font end
FONT_COLOR_END='\033[0m'

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

EMOJI_STAR_STRUCK="🤩"

main() {
    cd "$SCRIPT_DIR"
    check_your_environment
    build

    echo "構築が完了しました${EMOJI_STAR_STRUCK} 。http://localhost をWeb ブラウザで開いてWeb アプリ画面が表示されることを確認してください。"

    return 0
}

build() {
    git submodule update --init --recursive

    cd laradock
    cp env-example .env

    echo ''                         >> .env
    echo 'DB_HOST=mysql'            >> .env
    echo '# REDIS_HOST=redis'       >> .env
    echo 'QUEUE_HOST=beanstalkd'    >> .env

    docker-compose up -d nginx mysql workspace

    sync ; sleep 2

    docker-compose exec workspace bash -c '
        if [[ -d /var/www/a6s-cloud ]]; then
            chown -R laradock:laradock /var/www/a6s-cloud
        else
            echo "ERROR: /var/www/a6s-cloud ディレクトリがありません。Laravel プロジェクトの作成に失敗しました。" >&2
            exit 1
        fi
    '

    docker-compose exec workspace runuser -l laradock -c '
        cd /var/www/a6s-cloud
        composer install
        if [[ ! -f .env ]]; then
            cp .env.example .env
            php artisan key:generate
        fi
    '

    if [[ ! -f nginx/sites/default.conf.bak ]]; then
        cp nginx/sites/default.conf nginx/sites/default.conf.bak
        cp nginx/sites/laravel.conf.example default.conf
        sed -i -e 's|\(.*root\) .*/var/www/public.*|\1 /var/www/a6s-cloud/public;|g' nginx/sites/default.conf
    fi

    docker-compose stop && docker-compose up -d nginx mysql workspace
}

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

main "$@"

