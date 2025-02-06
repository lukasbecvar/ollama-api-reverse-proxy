#!/bin/bash

# clear console history
clear

# run tests process
docker-compose run --no-deps php bash -c "
    php vendor/bin/phpcbf &&
    php vendor/bin/phpcs &&
    php vendor/bin/phpstan analyze &&
    php bin/phpunit 2>/dev/null
"
