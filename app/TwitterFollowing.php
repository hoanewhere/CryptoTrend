<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwitterFollowing extends Model
{
    protected $fillable = [
        'login_user_id', 'searched_account_id', 'following', 
    ];
}
