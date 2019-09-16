<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SearchedAccount extends Model
{
    protected $fillable = [
        'twitter_account', 'twitter_user_id', 'profile_img_url', 'follow_cnt', 'follower_cnt', 'discription', 'recent_tweet_json', 'follow_flg', 'login_user_id'
    ];
}
