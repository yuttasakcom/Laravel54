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
