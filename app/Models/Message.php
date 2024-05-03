<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Message extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $hidden = ['media'];

    protected $fillable = [
        'content',
        'sender_id',
        'conversation_id',
        'message_type',
        'photos',
    ];

    protected $appends = ['medias'];

    protected $with = ["sender"];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('medias');
    }

    public function getMediasAttribute()
    {
        $medias = $this->getMedia('medias');
        $requiredAttributes = [];
        foreach ($medias as $media) {
            $attributes = array(
                "original_url" => $media->getFullUrl(),
                "file_name" => $media->file_name,
                "size" => $media->size,
                "human_readable_size" => $media->human_readable_size,
                "mime_type" => $media->mime_type,
            );
            array_push($requiredAttributes, $attributes);
        }
        return $requiredAttributes;
    }

    public function sender()
    {
        return $this->belongsTo('App\Models\User', 'sender_id', 'id');
    }
}
