<?php

use Illuminate\Http\Request;
use App\User;
use App\Post;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
|--------------------------------------------------------------------------
| Database Raw SQL Queries
|--------------------------------------------------------------------------
*/

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
  // $results = \DB::select('select * from posts where id = ?', [1]);
    $results = \DB::select('select * from posts');

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
  $post->user_id = 1;
  $post->save();
});

Route::get('/orm/insert2', function() {
  $post = Post::find(2);
  $post->title = 'New Eloquent title insert 2';
  $post->content = 'Wow eloquent';
  $post->user_id = 1;
  $post->save();
});

Route::get('/orm/create', function() {
  Post::create([
    'title' => 'test create title',
    'content' => 'test create content',
    'user_id' => 1,
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
  Post::find(6)->delete();
});

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

/*
|--------------------------------------------------------------------------
| Database Eloquent/ORM Relationships
|--------------------------------------------------------------------------
*/

// One to One relationship
Route::get('/user/{id}/posts', function($id) {
  return User::find($id)->posts;
});

Route::get('/post/{id}/user', function($id) {
  return Post::find($id)->user;
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// One to Many relationship
Route::get('/posts', function() {
    $user = User::find(1);

    foreach ($user->posts as $post) {
      echo $post->title . "<br>";
    }

    // return $user;
});

// Many to Many relationship
// php artisan make:model Role -m
// php artisan make:migration create_users-roles_table --create=role_user
Route::get('/user/{id}/role', function($id) {
  // $user = User::find($id);
  // foreach ($user->roles as $role) {
  //   echo $role->name . "<br>";
  // }
  $user = User::find($id)->roles()->orderBy('name', 'asc')->get();
  return $user;
});

// Accessing the intermediate table / pivot
Route::get('/user/pivot', function() {
  $user = User::find(1);

  foreach ($user->roles as $role) {}
    echo $role->pivot->created_at . "<br>";
});

// Has many through relationship
// php artisan make:model Country -m
// php artisan make:migration add_country_id_column_to_users --table=users
// sudo chown yo www/src/* -R
// php artisan migrate
Route::get('/user/country/{id}/post', function($id) {
  // $country = \App\Country::find($id);
  // foreach ($country->posts as $post) {
  //   return $post->title;
  // }
  return \App\Country::find($id)->posts()->get();
});

// Polymorphic relationship
// php artisan make:model Photo -m
// sudo chown yo www/src/* -R
Route::get('/user/photos', function() {
    $photos = User::find(1)->photos()->get();
    return $photos;
});
Route::get('/post/photos', function() {
  $photos = Post::find(3)->photos()->get();
  return $photos;
});

// Polymorphic relation the inverse
Route::get('/photo/{id}', function($id) {
    $morph = \App\Photo::find($id);
    return $morph->imageable;
});

// Polymorphic relation many to many
// php artisan make:model Video -m
// php artisan make:model Tags -m
// php artisan make:model Taggables -m
// sudo chown yo www/src/* -R
// php artisan migrate
Route::get('/post/tag', function() {
  return Post::find(5)->tags()->get();
});
Route::get('/tag/post', function() {
  return \App\Tag::find(2)->posts()->get();
});