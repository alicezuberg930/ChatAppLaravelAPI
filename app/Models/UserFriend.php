<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFriend extends Model
{
 
    protected $table = "user_friend";

    // protected $with = ['friend'];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function friend()
    {
        return $this->belongsTo('App\Models\User', 'friend_id', 'id');
    }
}
