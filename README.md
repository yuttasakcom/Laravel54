## Laravel5.4

## Create TLS
> openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout ssl/default.key -out ssl/default.crt

## Set Up
step1
> git clone git@github.com:yuttasakcom/Laravel54.git && cd Laravel54

step2
> docker-compose up -d --build

step3
>cd www/src/ && composer install

step4
> chmod 777 storage -R

step5
> go to http://localhost:8086 create database 'homestead'

step6
> php artisan migrate

step7
> go to http://localhost:8081


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