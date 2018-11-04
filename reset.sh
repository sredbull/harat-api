#!/usr/bin/env bash
docker exec -it harat-php-fpm bin/console doctrine:cache:clear-result
docker exec -it harat-php-fpm bin/console doctrine:cache:clear-query
docker exec -it harat-php-fpm bin/console doctrine:cache:clear-metadata
#docker exec -it harat-php-fpm bin/console doctrine:database:drop --force
#docker exec -it harat-php-fpm bin/console doctrine:database:create
#docker exec -it harat-php-fpm bin/console doctrine:schema:update --force
docker exec -it harat-php-fpm bin/console doctrine:schema:update --dump-sql
