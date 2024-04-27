<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'recent_message',
        'is_read',
        'recipient_id',
        'recipient_group_id',
    ];

    protected $with = ["recipient_group", "recipient_user"];

    public function recipient_group()
    {
        return $this->belongsTo('App\Models\Group', 'recipient_group_id', 'id');
    }

    public function recipient_user()
    {
        return $this->belongsTo('App\Models\User', 'recipient_id', 'id');
    }

    public function messages()
    {
        return $this->hasMany('App\Models\Message', 'conversation_id', 'id');
    }
}
