#!/usr/bin/env bash

docker-compose down
docker-compose run --rm unittest
docker-compose run --rm unittest php ./vendor/bin/php-coveralls -v -x ./build/logs/clover.xml

docker-compose down
docker container prune -f