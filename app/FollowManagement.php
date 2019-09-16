<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FollowManagement extends Model
{
    protected $fillable = [
        'quarter_cnt', 'day_cnt', 'login_user_id'
    ];
}
