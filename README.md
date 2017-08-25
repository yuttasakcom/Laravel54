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

change permission
> docker exec lara chmod 777 storage -R

good luck!
> go to http://localhost:8081

### (สำหรับผู้เริ่มต้น) เรียนรู้การสร้างโปรเจ็กต์ด้วย Laravel5.4
## Table of Contents
- Part 1 Database Design
  - [Migration](#migration)

... กำลังจัดทำเนื้อหา

### Part 1 Database Design
## Migration
create migration
> php artisan make:migration create_posts_table --create="posts"

ทำการแก้ไขไฟล์ที่ www/src/database/migrations/...create_posts_table.php
```php
public function up()
{
    Schema::create('posts', function (Blueprint $table) {
        $table->increments('id');
        $table->string('title'); // create column title data type to string
        $table->text('content'); // create column content data type to text
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
  \DB::insert('insert into posts(title, content, user_id) values(?, ?, ?)', [
    'title 1',
    'content 1',
    1,
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
  $updated = \DB::update('update posts set title="update title 1" where id = ?', [1]);
  return $updated;
});

// delete
Route::get('/raw/delete', function() {
  $deleted = \DB::delete('delete from posts where id = ?', [1]);
  return $deleted;
});

```

## Database - Eloquent/ORM
```php
/*
|--------------------------------------------------------------------------
| Database Eloquent/ORM
|--------------------------------------------------------------------------
*/
Route::get('/orm/read', function() {
    $posts = Post::all();

    return $posts;
});

Route::get('/orm/find', function() {
  $post = Post::find(2);

  return $post->title;
});

Route::get('/orm/findwhere', function() {
  $post = Post::where('id', 3)->orderBy('id', 'desc')->take(1)->get();

  return $post;
});

Route::get('/orm/findmore', function() {
  // $post = Post::findOrFail(1);
  $posts = Post::where('users_count', '<', 50)->firstOrFail();

  return $post;
});

Route::get('/orm/insert', function() {
  $post = new Post;
  $post->title = 'New Eloquent title insert';
  $post->content = 'Wow eloquent';
  $post->save();
});

Route::get('/orm/insert2', function() {
  $post = Post::find(2);
  $post->title = 'New Eloquent title insert 2';
  $post->content = 'Wow eloquent';
  $post->save();
});

Route::get('/orm/create', function() {
  Post::create([
    'title' => 'test create title',
    'content' => 'test create content'
  ]);
});

Route::get('/orm/update', function() {
  Post::where('id', 2)->where('is_admin', 0)->update([
    'title' => 'test update title',
    'content' => 'test update content'
  ]);
});

Route::get('/orm/delete', function() {
  $post = Post::find(1);
  $post->delete();
});

Route::get('/orm/delete2', function() {
  Post::destroy(2);
  // Post::destroy([2, 3]);
  // Post::where('is_admin', 0)->delete();
});

Route::get('/orm/softdelete', function() {
  Post::find(1)->delete();
});

===== Model =====

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;

    protected $table = 'posts';
    // protected $primaryKey = 'post_id';

    protected $datas = ['deleted_at'];

    // Allow กรณีใช้ Post::create([...])
    protected $fillable = [
      'title',
      'content',
    ];
}

=================

Route::get('/orm/readsoftdelete', function() {
  // $post = Post::find(6);
  // return $post;

  // $post = Post::withTrashed()->where('id', 3)->get();

  $post = Post::onlyTrashed()->get();
  return $post;
});

Route::get('/orm/restore', function() {
  Post::withTrashed()->where('is_admin', 0)->restore();
});

Route::get('/orm/forcedelete', function() {
  Post::withTrashed()->where('is_admin', 0)->forceDelete();
});

```

## Database Eloquent/ORM Relationships

```php
/*
|--------------------------------------------------------------------------
| Database Eloquent/ORM Relationships
|--------------------------------------------------------------------------
*/

// One to One relationship
Route::get('/user/{id}/post', function($id) {
  return User::find($id)->post;
});

===== Model =====

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;

    protected $table = 'posts';
    // protected $primaryKey = 'post_id';

    protected $datas = ['deleted_at'];

    // Allow กรณีใช้ Post::create([...])
    protected $fillable = [
      'title',
      'content',
    ];

    public function user() {
      return $this->belongsTo('App\User');
    }
}

=================

Route::get('/post/{id}/user', function($id) {
  return Post::find($id)->user;
});

===== Model =====

<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function post() {
      return $this->hasOne('App\Post');
    }
}

=================

```