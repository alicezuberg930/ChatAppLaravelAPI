<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Message extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'messages';
    protected $fillable = [
        'content',
        'sender_id',
        'conversation_id',
        'message_type',
        'photos',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('default');
    }

}
