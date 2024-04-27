<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Message extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $hidden = [
        // 'media'
    ];

    protected $fillable = [
        'content',
        'sender_id',
        'conversation_id',
        'message_type',
        'photos',
    ];

    protected $appends = ['medias'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('medias');
    }

    public function getMediasAttribute()
    {
        return $this->getMedia('medias');
    }
}
