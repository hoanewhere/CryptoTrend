<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UpdatedTime extends Model
{
    protected $fillable = [
        'time_index', 'login_user_id', 'complete_flg'
    ];
}
