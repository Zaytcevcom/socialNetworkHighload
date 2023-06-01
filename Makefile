pull: pull-git pull-composer pull-migrate pull-clear

pull-git:
	git pull

pull-composer:
	/www/server/php/81/bin/php composer.phar install --no-interaction

pull-migrate:
	/www/server/php/81/bin/php composer.phar app-server migrations:migrate

pull-clear:
	rm -rf var/cache/* var/log/*

init: init-ci
init-ci: docker-down-clear \
	app-clear \
	docker-pull docker-build docker-up \
	app-init

up: docker-up
down: docker-down
restart: down up

#linter and code-style
lint: app-lint
analyze: app-analyze
validate-schema: app-db-validate-schema
cs-fix: app-cs-fix
test: app-test

update-deps: app-composer-update

#check all
check: lint analyze validate-schema #test

#Docker
docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build --pull

app-clear:
	docker run --rm -v ${PWD}/:/app -w /app alpine sh -c 'rm -rf var/cache/* var/log/* var/test/* var/mysql/* var/replication/*'


#Composer
app-init: app-permissions app-composer-install \
	app-wait-db-source app-wait-db-replica-1 app-wait-db-replica-2 app-wait-haproxy \
	app-wait-redis app-wait-rabbitmq \
	setup-semi-sync-replication \
	app-db-migrations app-db-fixtures

app-permissions:
	docker run --rm -v ${PWD}/:/app -w /app alpine chmod 777 var/cache var/log var/test var/mysql var/replication

app-composer-install:
	docker-compose run --rm php-cli composer install

app-composer-update:
	docker-compose run --rm php-cli composer update

app-composer-autoload: #refresh autoloader
	docker-compose run --rm php-cli composer dump-autoload

app-composer-outdated: #get not updated
	docker-compose run --rm php-cli composer outdated

app-wait-db-source:
	docker-compose run --rm php-cli wait-for-it db-source:3306 -t 30

app-wait-haproxy:
	docker-compose run --rm php-cli wait-for-it haproxy:3306 -t 30

app-wait-db-replica-1:
	docker-compose run --rm php-cli wait-for-it db-replica-1:3306 -t 30

app-wait-db-replica-2:
	docker-compose run --rm php-cli wait-for-it db-replica-2:3306 -t 30

app-wait-redis:
	docker-compose run --rm php-cli wait-for-it hl-redis:6379 -t 30

app-wait-rabbitmq:
	docker-compose run --rm php-cli wait-for-it hl-rabbitmq:5672 -t 60


#DB
app-db-validate-schema:
	docker-compose run --rm php-cli composer app orm:validate-schema

app-db-migrations-diff:
	docker-compose run --rm php-cli composer app migrations:diff

app-db-migrations:
	docker-compose run --rm php-cli composer app migrations:migrate -- --no-interaction

app-db-fixtures:
	docker-compose run --rm php-cli composer app fixtures:load


#Lint and analyze
app-lint:
	docker-compose run --rm php-cli composer lint
	docker-compose run --rm php-cli composer php-cs-fixer fix -- --dry-run --diff

app-cs-fix:
	docker-compose run --rm php-cli composer php-cs-fixer fix

app-analyze:
	docker-compose run --rm php-cli composer psalm


#Tests
app-test:
	docker-compose run --rm php-cli composer test

app-test-coverage:
	docker-compose run --rm php-cli composer test-coverage

app-test-unit:
	docker-compose run --rm php-cli composer test -- --testsuite=unit

app-test-unit-coverage:
	docker-compose run --rm php-cli composer test-coverage -- --testsuite=unit

app-test-functional:
	docker-compose run --rm php-cli composer test -- --testsuite=functional

app-test-functional-coverage:
	docker-compose run --rm php-cli composer test-coverage -- --testsuite=functional


#Console
console:
	docker-compose run --rm php-cli composer app


#Setup semi-sync replication
setup-semi-sync-replication: setup-semi-sync-replication-source setup-semi-sync-replication-replica-1 setup-semi-sync-replication-replica-2

setup-semi-sync-replication-source:
	docker exec -it -e MYSQL_PWD=1234567890 hl-mysql-source mysql -u root \
	-e "\
		INSTALL PLUGIN rpl_semi_sync_source SONAME 'semisync_source.so'; \
		INSTALL PLUGIN rpl_semi_sync_replica SONAME 'semisync_replica.so'; \
		SET GLOBAL rpl_semi_sync_source_enabled = 1; \
		CREATE USER 'replication'@'%' IDENTIFIED BY '1234567890'; \
		GRANT REPLICATION SLAVE ON *.* TO 'replication'@'%'; \
		FLUSH PRIVILEGES; \
		ALTER USER 'replication'@'%' IDENTIFIED WITH mysql_native_password BY '1234567890'; \
		SHOW MASTER STATUS; \
	"

setup-semi-sync-replication-replica-1:
	docker exec -it -e MYSQL_PWD=1234567890 hl-mysql-replica-1 mysql -u root \
	-e "\
		INSTALL PLUGIN rpl_semi_sync_source SONAME 'semisync_source.so'; \
		INSTALL PLUGIN rpl_semi_sync_replica SONAME 'semisync_replica.so'; \
		SET GLOBAL rpl_semi_sync_replica_enabled = 1; \
		CHANGE REPLICATION SOURCE TO SOURCE_HOST='hl-mysql-source', SOURCE_USER='replication', SOURCE_PASSWORD='1234567890', SOURCE_AUTO_POSITION=1; \
		START REPLICA; \
		SHOW REPLICA STATUS; \
    "

setup-semi-sync-replication-replica-2:
	docker exec -it -e MYSQL_PWD=1234567890 hl-mysql-replica-2 mysql -u root \
	-e "\
		INSTALL PLUGIN rpl_semi_sync_source SONAME 'semisync_source.so'; \
		INSTALL PLUGIN rpl_semi_sync_replica SONAME 'semisync_replica.so'; \
		SET GLOBAL rpl_semi_sync_replica_enabled = 1; \
		CHANGE REPLICATION SOURCE TO SOURCE_HOST='hl-mysql-source', SOURCE_USER='replication', SOURCE_PASSWORD='1234567890', SOURCE_AUTO_POSITION=1; \
		START REPLICA; \
		SHOW REPLICA STATUS; \
    "

#docker-compose run --rm php-cli composer require monolog/monolog
#docker-compose run --rm php-cli composer outdated --direct - просмотр мажорных обновлений
#docker-compose run --rm php-cli composer update --with-dependencies vimeo/psalm
#docker-compose run --rm php-cli composer app migrations:diff
