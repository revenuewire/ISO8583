#!/usr/bin/env bash

docker-compose down
docker-compose run --rm unittest
docker-compose down
docker container prune -f