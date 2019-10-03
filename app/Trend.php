<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Trend extends Model
{
    public function crypto() {
        return $this->belongsTo('App\Crypto');
    }

    protected $fillable = [
        'crypto_id', 'transaction_price_max', 'transaction_price_min', 'time_id', 'complete_flg', 'next_params', 'hour_cnt', 'day_cnt', 'week_cnt' 
    ];
}
