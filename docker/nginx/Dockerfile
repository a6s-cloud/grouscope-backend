FROM nginx:1.17
MAINTAINER Tsutomu Nakamura<tsuna.0x00@gmail.com>

RUN apt-get update && \
        apt-get -y upgrade
COPY default.conf /etc/nginx/conf.d/default.conf
