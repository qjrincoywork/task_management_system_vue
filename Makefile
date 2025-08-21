SHELL := /bin/sh
SAIL = ./backend/vendor/bin/sail
WEB_CONTAINER = $(if $(APP_SERVICE),$(APP_SERVICE),web)

PHP_EXEC := $(if $(APP_SERVICE),$(SAIL),php)

clear-cache:
	$(PHP_EXEC) artisan cache:clear
	$(PHP_EXEC) artisan view:clear
	$(PHP_EXEC) artisan route:clear
	$(PHP_EXEC) artisan config:clear

up:
	docker compose up -d --build

down:
	docker compose down -v

logs:
	docker compose logs -f --tail=200

backend-install:
	docker compose run --rm backend bash -lc "if [ ! -f composer.json ]; then composer create-project laravel/laravel .; fi && composer install && php artisan key:generate"

frontend-install:
	docker compose run --rm frontend sh -lc "if [ ! -f package.json ]; then npm create vue@latest . -- --default; fi && npm i && npm i -D tailwindcss postcss autoprefixer && npx tailwindcss init -p"

reset:
	$(MAKE) down && rm -rf backend frontend && $(MAKE) up

setup-local:
	chmod +x ./setup.sh
	./setup.sh $(WEB_CONTAINER)


