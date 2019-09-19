<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Coincheck\Coincheck;
use Illuminate\Support\Facades\Log;


class CoinCheckController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Coin Check Controller
    |--------------------------------------------------------------------------
    |
    | CoinCheck APIにアクセスするコントローラ
    |
    */

    public static function getTransactionPrice() {
        $config = config('coincheck');
        $coincheck = new Coincheck($config['access_key'], $config['secret_key']);
        $res = $coincheck->ticker->all();
        Log::debug('coincheck.tickerの結果:'. print_r($res, true));
        return $res;
    }
}
