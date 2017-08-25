## Docker for Laravel5.4

## Create TLS
> openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout ssl/default.key -out ssl/default.crt

## Set Up
step1
> git clone git@github.com:yuttasakcom/Laravel54.git && cd Laravel54

step2
> docker-compose up -d --build

step3
> docker exec php composer install

step4
> docker exec php chmod 777 storage -R

step5
> go to http://localhost:8086 create database 'homestead'

step6
> docker exec php php artisan migrate

step7
> go to http://localhost:8081