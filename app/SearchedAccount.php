<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SearchedAccount extends Model
{
    protected $fillable = [
        'account_data', 'update_time_id', 'login_user_id'
    ];
}
