## Docker for Laravel5.4

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