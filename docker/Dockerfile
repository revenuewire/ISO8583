FROM revenuewire/docker-php7-alpine:1.0.2

RUN apk update
RUN apk add php7-xdebug php7-dom php7-tokenizer
RUN apk upgrade

COPY docker/xdebug.ini /etc/php7/conf.d/xdebug.ini
