<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use App\Models\User;
// use App\Models\Like;
// use App\Models\Comment;

class Post extends Model
{
    protected $fillable = ['user_id', 'content', 'media_type', 'media_path'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public $timestamps = true;
}
