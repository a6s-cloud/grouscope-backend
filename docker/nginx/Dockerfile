# docker build -t a6scloud/nginx .
# docker run --name laravel --hostname laravel --net a6s -ti a6scloud/nginx
FROM nginx:1.17
MAINTAINER Tsutomu Nakamura<tsuna.0x00@gmail.com>

RUN apt-get update && \
        apt-get -y upgrade
COPY default.conf /etc/nginx/conf.d/default.conf
