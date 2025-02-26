#!/bin/bash

set -e

./vendor/bin/sail bash -c "PHP_CS_FIXER_IGNORE_ENV=1 php ./vendor/bin/php-cs-fixer fix"
./vendor/bin/sail bash -c "./vendor/bin/phpstan analyse app tests"
./vendor/bin/sail artisan test

