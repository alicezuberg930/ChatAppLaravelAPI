<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages';
    protected $fillable = [
        'content',
        'sender_id',
        'conversation_id',
        'message_type',
        'photos',
    ];
}
