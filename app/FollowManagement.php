<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FollowManagement extends Model
{
    protected $fillable = [
        'auto_follow_flg', 'day_cnt', 'login_user_id', 'day_limit_flg'
    ];
}
