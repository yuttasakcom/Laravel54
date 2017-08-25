## Laravel5.4
> ติดตั้งและพัฒนาโปรเจ็กต์ด้วย Laravel5.4 บน Docker

## Required
- Docker engine
- Docker compose
- Git

## Setup
clone project
> git clone git@github.com:yuttasakcom/Laravel54.git && cd Laravel54

create cert
> mkdir ssl && openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout ssl/default.key -out ssl/default.crt

build & run docker
> docker-compose up -d --build

install package
> cd www/src && composer install --prefer-dist -vvv
or
> docker exec lara composer install --prefer-dist -vvv

change permission
> docker exec lara chmod 777 storage -R

create database `username: root, password: password`
> go to http://localhost:8086 create database name 'homestead'

create env
> cd www/src && touch .env

copy text มาวาง และแก้ไขข้อมูล DB [.env.example](https://raw.githubusercontent.com/laravel/laravel/master/.env.example)
```
DB_CONNECTION=mysql
DB_HOST=mariadb
DB_PORT=3306
DB_DATABASE=homestead
DB_USERNAME=root
DB_PASSWORD=password
```

generate key
> docker exec lara php artisan key:generate

migrate
> docker exec lara php artisan migrate

good luck!
> go to http://localhost:8081

## Table of Contents
- Part 1 Database Design
  - [Migration](#migration)
... กำลังจัดทำ

### (สำหรับผู้เริ่มต้น) เรียนรู้การสร้างโปรเจ็กต์ด้วย Laravel5.4<br>
### Part 1 Database Design
## Migration
create migration
> php artisan make:migration create_posts_table --create="posts"

```php
public function up()
{
    Schema::create('posts', function (Blueprint $table) {
        $table->increments('id');
        $table->string('title'); // create column title data type to string
        $table->text('body'); // create column body data type to text
        $table->timestamps();
    });
}
```

drop table
> php artisan migrate:rollback

migrate column
> php artisan make:migration add_is_admin_column_to_posts_tables --table="posts"
```php
public function up()
{
    Schema::table('posts', function (Blueprint $table) {
        $table->integer('is_admin')->unsigned();
    });
}

public function down()
{
    Schema::table('posts', function (Blueprint $table) {
        $table->dropColumn('is_admin');
    });
}
```
> php artisan migrate
> php artisan migrate:rollback
> php artisan migrate:refresh
> php artisan migrate:status

## Database Raw SQL Queries
```php
// insert
Route::get('/raw/insert', function(Request $request) {
  \DB::insert('insert into posts(title, body, is_admin) values(?, ?, ?)', [
    'test1',
    'test2',
    0,
  ]);
});

// select
Route::get('/raw/read', function() {
  $results = \DB::select('select * from posts where id = ?', [1]);

  // foreach ($results as $result) {
  //   return $result->title;
  // }
  return $results;
});

// update
Route::get('/raw/update', function() {
  $updated = \DB::update('update posts set title="test update" where id = ?', [1]);
  return $updated;
});

// delete
Route::get('/raw/delete', function() {
  $deleted = \DB::delete('delete from posts where id = ?', [1]);
  return $deleted;
});

// Database Eloquent/ORM

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
```

## Database - Eloquent/ORM
> php artisan make:model Post