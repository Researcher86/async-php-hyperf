build:
	docker-compose build

up: php-clear
	docker-compose up -d
	#docker-compose exec hyperf php bin/hyperf.php migrate

restart:
	docker-compose restart

logs:
	docker-compose logs

down:
	docker-compose down

down-all:
	docker-compose down -v

php:
	docker-compose exec hyperf bash

redis:
	docker-compose exec redis redis-cli

gen-producer:
	docker-compose exec hyperf php bin/hyperf.php gen:amqp-producer DemoProducer

gen-consumer:
	docker-compose exec hyperf php bin/hyperf.php gen:amqp-consumer DemoConsumer

gen-command:
	docker-compose exec hyperf php bin/hyperf.php gen:command FooCommand

run-command:
	docker-compose exec hyperf php bin/hyperf.php demo:command

gen-migration:
	docker-compose exec hyperf php bin/hyperf.php gen:migration create_users_table

gen-model:
	docker-compose exec hyperf php bin/hyperf.php gen:model users

migrate:
	docker-compose exec hyperf php bin/hyperf.php migrate

php-clear:
	rm -rf app/runtime/container

# make bench
bench:
    # https://github-wiki-see.page/m/giltene/wrk2/wiki/Installing-wrk2-on-Linux#:~:text=Installing%20wrk2%20on,wrk%20and%20build.
	wrk -t4 -c100 -R100 http://host.docker.internal:9501
	wrk -t4 -c100 -R100 http://host.docker.internal:9501/test/index
	wrk -t4 -c100 -R100 http://host.docker.internal:9501/index.html

