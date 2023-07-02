#!/usr/bin/env bash

composer install -n --prefer-dist
#bin/console doctrine:migrations:migrate --no-interaction
#bin/console doctrine:fix:load --no-interaction

exec "$@"