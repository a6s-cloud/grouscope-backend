#!/usr/bin/env bash
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

function is_mac() {
    if [ "$(uname)" == "Darwin" ] || (command -v brew > /dev/null 2>&1); then
        return 0
    fi
    return 1
}

build() {
    rm -rf laradock
    git submodule update --init --recursive

    cd laradock
    cp env-example .env

    echo ''                         >> .env
    echo 'DB_HOST=mysql'            >> .env
    echo '# REDIS_HOST=redis'       >> .env
    echo 'QUEUE_HOST=beanstalkd'    >> .env

    if is_mac; then
        sed -i "" -e "s|^WORKSPACE_TIMEZONE=.*|WORKSPACE_TIMEZONE=Asia/Tokyo|g"  .env
    else
        sed -i -e "s|^WORKSPACE_TIMEZONE=.*|WORKSPACE_TIMEZONE=Asia/Tokyo|g"  .env
    fi

    docker-compose up -d nginx mysql workspace

    sync ; sleep 2

    docker-compose exec workspace bash -c '
        set -e
        if [[ -d /var/www/a6s-cloud ]]; then
            chown -R laradock:laradock /var/www/a6s-cloud
        else
            echo "ERROR: /var/www/a6s-cloud ディレクトリがありません。Laravel プロジェクトの作成に失敗しました。" >&2
            exit 1
        fi
    '

    docker-compose exec workspace runuser -l laradock -c '
        set -e
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

    init_mysql_db
    docker-compose stop && docker-compose up -d nginx mysql workspace
}

# Mysql DB のデータを初期化する
init_mysql_db() {
    echo "NOTICE: mysql データを初期化しています。"

    docker-compose exec mysql bash -c '
        set -e
        DB_PW_DEFAULT="secret"
        DB_PW_ROOT="root"
        DB_NAME="a6s_cloud"

        echo ">>> sql: CREATE DATABASE IF NOT EXISTS ${DB_NAME};"
        MYSQL_PWD=${DB_PW_ROOT} mysql -u root <<< "CREATE DATABASE IF NOT EXISTS ${DB_NAME};"

        echo ">>> DROP TABLE;"
        echo "SET FOREIGN_KEY_CHECKS=0;"                                                                    >  /tmp/drop_all_tables.sql
        MYSQL_PWD=${DB_PW_ROOT} mysqldump --add-drop-table --no-data -u root ${DB_NAME} | grep "DROP TABLE" >> /tmp/drop_all_tables.sql || true
        echo "SET FOREIGN_KEY_CHECKS=1;"                                                                    >> /tmp/drop_all_tables.sql

        if [[ ! -f /tmp/drop_all_tables.sql ]]; then
            echo "ERROR: File /tmp/drop_all_tables.sql is not found." >&2
            exit 1
        fi
        if [[ "$(wc -c < foo.txt)" -ne 0 ]]; then
            MYSQL_PWD=${DB_PW_ROOT} mysql -u root a6s_cloud                                                     <  /tmp/drop_all_tables.sql
        fi
        rm -f /tmp/drop_all_tables.sql

        # MYSQL_PWD="${DB_PW_ROOT}" mysql -u root <<< "SELECT user, host, plugin FROM mysql.user;" | grep -E "^default"
        echo ">>> sql: ALTER USER '"'"'default'"'"'@'"'"'%'"'"' IDENTIFIED WITH mysql_native_password BY '"'"'secret'"'"';"
        MYSQL_PWD="${DB_PW_ROOT}" mysql -u root <<< "ALTER USER '"'"'default'"'"'@'"'"'%'"'"' IDENTIFIED WITH mysql_native_password BY '"'"'secret'"'"';"
        # MYSQL_PWD="${DB_PW_ROOT}" mysql -u root <<< "SELECT user, host, plugin FROM mysql.user;" | grep -E "^default"
    '
    # MYSQL_PWD="${DB_PW_ROOT}" mysql -u root <<< "ALTER USER 'default'@'%' IDENTIFIED WITH mysql_native_password BY 'secret';"

    docker-compose exec workspace runuser -l laradock -c '
        set -e
        DB_PW_DEFAULT="secret"
        DB_PW_ROOT="root"
        DB_NAME="a6s_cloud"

        cd /var/www/a6s-cloud

        # .env ファイルはLaravel のプロジェクトを作成した時に自動的に
        # .gitignore に含まれており環境ごとに変えるべきというもののようなので都度.env ファイルの中身を編集する
        sed -i "s/^DB_HOST=.*/DB_HOST=mysql/g"                      .env
        sed -i "s/^DB_DATABASE=.*/DB_DATABASE=${DB_NAME}/g"         .env
        sed -i "s/^DB_USERNAME=.*/DB_USERNAME=default/g"            .env
        sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD=${DB_PW_DEFAULT}/g"   .env
        sync
        echo "NOTICE: プロジェクトのDB 接続先を設定しました"
        grep -E "(^DB_HOST|^DB_DATABASE|^DB_USERNAME)"              .env
        echo "DB_PASSWORD=**********"

        # TODO: モデル名Articles は仮名なのであとで正式なものに置換する
        # database/migrations/YYYY_MM_DD_HHMMSS_create_articles_table.php ファイル内のDB 定義の通りにテーブルを作成する
        php artisan migrate:refresh
        php artisan db:seed
    '

    docker-compose exec mysql bash -c '
        set -e
        DB_PW_ROOT="root"
        DB_NAME="a6s_cloud"

        echo "GRANT ALL ON ${DB_NAME}.* TO '"'"'default'"'"'@'"'"'%'"'"';"
        MYSQL_PWD="${DB_PW_ROOT}" mysql -u root <<< "GRANT ALL ON ${DB_NAME}.* TO '"'"'default'"'"'@'"'"'%'"'"';"
    '

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

