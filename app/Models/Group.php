<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Group extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'group_name',
        'admin_id',
    ];

    protected $hidden = [
        'media',
    ];

    protected $appends = ['avatar'];

    protected $with = ['users'];

    public function getAvatarAttribute()
    {
        return $this->getFirstMediaUrl('avatar');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->useFallbackUrl(asset('assets/images/avatar/') . '/default_group_avatar.png');
    }

    public function users()
    {
        return $this->hasMany('App\Models\UserGroup', 'group_id', 'id');
    }
}
