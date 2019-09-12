<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\TwitterController;
use App\Http\Controllers\CoinCheckController;

class TrendRankingController extends Controller
{
    public function index() {
        $hogehoge =  $this->searchTrends(1);
        Log::debug('$trends: '.print_r($hogehoge, true));
        return view('crypto.trendRanking');
    }


    /**
     * 仮想通貨のトレンド（ツイート数と取引価格）を取得する。
     * @param int $term_id
     * @return array $trends
     */
    private function searchTrends(int $term_id) {
        $trends = array();

        // cryptoテーブルから対象のcryptoを取得 
        $crypto_list = ['BTC', 'AAA'];// TBD: $crypto_list =; TBD:テーブルから読み出し

        // 仮想通貨の数だけループする
        foreach($crypto_list as $crypto) {
            // 条件からツイート情報取得
            $searched_tweet = TwitterController::searchTweet($crypto, $term_id); // TBD: メソッド未実装
            $tweet_cnt = $searched_tweet;// TBD: $searched_tweetからツイート数抜き出す

            // 仮想通貨の取引価格を取得
            $search_price = CoinCheckController::searchTransactionPrice($crypto); // TBD: メソッド未実装
            $crypto_max = $search_price; // TBD: $search_priceから最大取引価格を取り出す
            $crypto_min = $search_price; // TBD: $search_priceから最小取引価格を取り出す

            $trends[$crypto] = ['tweet_cnt'=>$tweet_cnt, 'transaction_price_max'=>$crypto_max, 'transaction_price_min'=>$crypto_min];
            Log::debug('$trends: '.print_r($trends, true));
        }
        Log::debug('$trends: '.print_r($trends, true));
        return $trends;
    }
}
