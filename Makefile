.PHONY:build

include .env

# Container management
######################
up:
	docker compose up -d

down:
	docker compose down

reup: down up

logs: 
	docker compose logs -f

build:
	docker compose build --no-cache
	make up
	make composer_install


# Container interaction
#######################
exec_app_bash:
	docker compose exec app bash

exec_app_cmd:
	docker compose exec app bash -c "${CMD}"


# Composer
##########
composer_install:
	make exec_app_cmd CMD="composer install"

composer_update:
	make exec_app_cmd CMD="composer update"

composer_test:
	make exec_app_cmd CMD="composer test"


# App
#####
app_console:
	make exec_app_cmd CMD="php bin/console"


# Shortcuts
###########
php: exec_app_bash
test: composer_test
