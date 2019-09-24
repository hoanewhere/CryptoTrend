<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Crypto extends Model
{
    public function trends() {
        return $this->hasMany('App\Trend');
    }

    protected $fillable = [
        'crypto'
    ];
}
