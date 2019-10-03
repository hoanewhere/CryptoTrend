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


    /**
     * 仮想通貨の取引価格（24時間での最大、最小額）を取得し、返す。（現状はBTCのみ）
     * 
     * @return array $res
     */
    public static function getTransactionPrice() {
        $config = config('coincheck');
        $coincheck = new Coincheck($config['access_key'], $config['secret_key']);
        $res = $coincheck->ticker->all();
        Log::debug('coincheck.tickerの結果:'. print_r($res, true));
        return $res;
    }
}
