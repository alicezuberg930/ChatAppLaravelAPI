<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCallChannel extends Model
{
    use HasFactory;

    protected $with = ['caller', 'receiver', 'group'];

    public function caller()
    {
        return $this->belongsTo('App\Models\User', 'caller_id', 'id');
    }

    public function receiver()
    {
        return $this->belongsTo('App\Models\User', 'receiver_id', 'id');
    }

    public function group()
    {
        return $this->belongsTo('App\Models\Group', 'group_id', 'id');
    }
}
