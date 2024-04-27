<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserConversation extends Model
{
    use HasFactory;

    protected $table = "user_conversation";

    protected $with = ["receiver", "conversation"];

    protected $fillable = ["user_id", "conversation_id", "receiver_id"];

    public function receiver()
    {
        return $this->belongsTo('App\Models\User', 'receiver_id', 'id');
    }

    public function conversation()
    {
        return $this->belongsTo('App\Models\Conversation', 'conversation_id', 'id');
    }
}
