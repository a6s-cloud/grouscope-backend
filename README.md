# a6s-cloud-backend

# OVERVIEW
`a6s-cloud` (analysis-cloud) は[twitter](https://twitter.com/)ハッシュタグ分析プラットフォームです。
## Description

a6s-cloud は [a6s-cloud-front](https://github.com/nsuzuki7713/a6s-cloud-front) と [a6s-cloud-backend](https://github.com/nsuzuki7713/a6s-cloud-backend) と[a6s-cloud-batch](https://github.com/nsuzuki7713/a6s-cloud-batch) の3つのプログラムにより構成されたSPA形式のアーキテクチャを採用しており、これはそのバックエンドを担うa6s-cloud-backend のリポジトリです。

`a6s-cloud` の概要、理念、開発スタイル、及び、利用方法は [a6s-cloud-front](https://github.com/nsuzuki7713/a6s-cloud-front) を御覧ください。

## For Developpers

larabel のコンテナ一式を起動する。

```
$ ./build.sh
```

コンテナを停止する。

```
$ pushd laradock
$ docker-compose stop
$ popd
```

コンテナを再起動する(build.sh をもう一回実行すればOK)。

```
$ ./build.sh
```

コンテナとイメージを削除する。イメージレベルでの修正が入った場合は、資材をpull してからこれを実施(数分〜数十分再起動にかかる)。

```
$ pushd laradock
$ docker-compose stop
$ docker-compose rm -f
$ docker images | grep -E '^laradock/.*' | awk '{print $3}'
$ popd
$ ./biuld.sh
```
