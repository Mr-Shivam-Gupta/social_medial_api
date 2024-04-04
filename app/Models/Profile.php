<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Follower;

class Profile extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'name', 'email', 'profile_picture', 'privacy','total_posts', 'total_likes'];

    public function user()
    {
        return $this->belongsTo(User::class); 
    }
    public function followers() {
        return $this->belongsToMany(Follower::class, 'followers', 'user_id', 'follower_id');
    }
    public $timestamps = true;
}
