build:
	docker-compose build

up: c-c migrate
	docker-compose up -d

restart:
	docker-compose restart

down:
	docker-compose down -v

php:
	docker-compose exec hyperf bash

redis:
	docker-compose exec redis redis-cli

gen-producer:
	docker-compose exec hyperf php bin/hyperf.php gen:amqp-producer DemoProducer

gen-consumer:
	docker-compose exec hyperf php bin/hyperf.php gen:amqp-consumer DemoConsumer

gen-migration:
	docker-compose exec hyperf php bin/hyperf.php gen:migration create_users_table

gen-model:
	docker-compose exec hyperf php bin/hyperf.php gen:model users

migrate:
	docker-compose exec hyperf php bin/hyperf.php migrate

c-c:
	rm -rf app/runtime/container

# make bench
bench:
    # https://github-wiki-see.page/m/giltene/wrk2/wiki/Installing-wrk2-on-Linux#:~:text=Installing%20wrk2%20on,wrk%20and%20build.
	wrk -t4 -c100 -R100 http://host.docker.internal:9501
	wrk -t4 -c100 -R100 http://host.docker.internal:9501/test/index
	wrk -t4 -c100 -R100 http://host.docker.internal:9501/index.html

