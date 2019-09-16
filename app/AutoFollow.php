<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AutoFollow extends Model
{
    protected $fillable = [
        'auto_follow_flg', 'login_user_id'
    ];
}
