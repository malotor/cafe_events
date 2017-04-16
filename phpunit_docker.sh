#!/bin/bash
docker run --rm -it --network eventscafe_default -e PHP_IDE_CONFIG="serverName=app" -v $PWD:/code -w /code eventscafe_app vendor/bin/phpunit $@