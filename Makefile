# Makefile for Docker Nginx PHP Composer MySQL

#include .env

# MySQL
#MYSQL_DUMPS_DIR=web/public/mysql

down:
	docker-compose down
up:
	docker-compose up -d
migrate:
	docker run --network host task2restapi_migration -path=/migrations/ -database "mysql://root:root@tcp(task2.loc:8989)/test" up


