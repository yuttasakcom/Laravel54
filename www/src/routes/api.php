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

// Database Raw SQL Queries
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
  // $results = \DB::select('select * from posts where id = ?', [1]);
    $results = \DB::select('select * from posts');

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
