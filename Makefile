APP_NAME=store_api
WORKDIR=/var/www/app
YOUR_ID=$(shell id -u)
YOUR_GROUP_ID=$(shell id -g)

EXEC_COMPOSE=docker compose exec -u $(YOUR_ID):$(YOUR_GROUP_ID) $(APP_NAME)

.PHONY: install up down restart bash server-logs db-fresh migration-create

install:
	docker compose down -v --remove-orphans
	docker compose build
	docker compose run --rm -u $(YOUR_ID):$(YOUR_GROUP_ID) --entrypoint "" $(APP_NAME) composer install
	docker compose up -d
	sleep 5
	$(EXEC_COMPOSE) sh -c "bin/console cache:clear"
	$(EXEC_COMPOSE) sh -c "bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration"
	$(EXEC_COMPOSE) sh -c "bin/console app:fixtures" || true

up:
	docker compose up -d

down:
	docker compose down

restart:
	docker compose restart

bash:
	docker compose exec -it -u $(YOUR_ID):$(YOUR_GROUP_ID) $(APP_NAME) bash

server-logs:
	docker compose logs -f

migration-create:
	$(EXEC_COMPOSE) sh -c "bin/console doctrine:migrations:generate"

db-migrate:
	$(EXEC_COMPOSE) sh -c "bin/console doctrine:migrations:migrate --no-interaction"

db-fresh:
	docker compose exec -T store_database mysql -ustore_user -pstore_secret_pass -e "DROP DATABASE IF EXISTS store_db; CREATE DATABASE store_db;"
	$(EXEC_COMPOSE) sh -c "bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration"
	$(EXEC_COMPOSE) sh -c "bin/console app:fixtures"

cache-clear:
	$(EXEC_COMPOSE) sh -c "bin/console cache:clear"