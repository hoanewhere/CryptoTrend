<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\TwitterController;
use App\Http\Controllers\CoinCheckController;

class TrendRankingController extends Controller
{




    public function index() {
        // $hogehoge =  $this->searchTrends(1);
        // Log::debug('$trends: '.print_r($hogehoge, true));

        $this->aggregateTweetTrend();
        return view('crypto.trendRanking');
    }


    /**
     * 仮想通貨のトレンド（ツイート数と取引価格）を取得する。
     * 
     * @return array $trends
     */
    // private function searchTrends() {
    //     Log::debug('searchTrends(関数呼び出し)');
    //     $trends = array();

    //     // cryptoテーブルから対象のcryptoを取得 
    //     $crypto_list = ['BTC', 'AAA'];// TBD: $crypto_list =; TBD:テーブルから読み出し

    //     // 仮想通貨の数だけループする
    //     foreach($crypto_list as $crypto) {
    //         // 条件からツイート情報取得
    //         $searched_tweet = TwitterController::countTweet($crypto); // TBD: メソッド未実装
    //         $tweet_cnt = $searched_tweet;// TBD: $searched_tweetからツイート数抜き出す

    //         // 仮想通貨の取引価格を取得
    //         $search_price = CoinCheckController::searchTransactionPrice($crypto); // TBD: メソッド未実装
    //         $crypto_max = $search_price; // TBD: $search_priceから最大取引価格を取り出す
    //         $crypto_min = $search_price; // TBD: $search_priceから最小取引価格を取り出す

    //         $trends[$crypto] = ['tweet_cnt'=>$tweet_cnt, 'transaction_price_max'=>$crypto_max, 'transaction_price_min'=>$crypto_min];
    //         Log::debug('$trends: '.print_r($trends, true));
    //     }
    //     Log::debug('$trends: '.print_r($trends, true));
    //     return $trends;
    // }

    public function aggregateTweetTrend() {
        Log::debug('aggregateTweetTrend(関数呼び出し)');
        // 現在の日付でtimeテーブル呼び出し
        $time_flg = false; // TBD: タイムテーブル呼び出してあるかないかを判定
        $complete_flg = false; // TBD: timeテーブルの完了フラグをセット

        if($time_flg) { //現在日付のレコードがある場合
            if ($complete_flg) { // 現在日付の情報取得が完了している場合
                return;
            } else {
                $start_day = '2019-09-11 00:00:00'; // TBD: timestampからstringに変換。右記参照 $start_day = date('Y-m-d_H:i:s', strtotime(timeテーブルのtimestamp));
                $crypto_data = $this->resumeSearch($start_day); //TBD: 再開処理開始
            }
        } else { //現在日付のレコードがない場合
            // TBD: timeテーブルにレコード追加
            
            $start_day = date("Y-m-d H:i:s");
            $crypto_data = $this->startSearch($start_day); // TBD: 新規処理開始
        }

        // 集計結果をもとにDB更新
        //TBD:
        Log::debug('集計結果: '.print_r($crypto_data, true));

        // 集計が完了したか確認する
        foreach($crypto_data as $item) {
            if($item['complete_flg'] === false) {
                return; // 一つでも未完了のものがあれば処理終了
            }
        }
        // 全て完了の場合、timeテーブルのcomplete_flgを完了にして処理終了
        // TBD:

    }

    private function startSearch(string $start_day) {
        Log::debug('startSearch(関数呼び出し)');

        $crypto_list = ['月代', '匕首']; // TBD: crypto_list取得 ←　別関数
        $crypto_data = $this->createCryptoData($crypto_list); // crypto_listからtmp生成
        $crypto_data = $this->searchForDetails($start_day, $crypto_data, $crypto_list);
        return $crypto_data;
    }

    private function resumeSearch(string $start_day) {
        Log::debug('resumeSearch(関数呼び出し)');

        $crypto_list = array(); // TBD: crypto_list取得 ←　別関数(getCryotoList)
        $crypto_data = array(); // DBからデータ取得してtmp生成
        $crypto_data = $this->searchForDetails($start_day, $crypto_data, $crypto_list);
        return $crypto_data;
    }

    private function searchForDetails(string $start_day, array $crypto_data, array $crypto_list){
        Log::debug('searchForDetails(関数呼び出し)');
        Log::debug('引数:start_day:' . $start_day);
        Log::debug('引数:crypto_data:' . print_r($crypto_data, true));
        Log::debug('引数:crypto_list:' . print_r($crypto_list, true));

        // 仮想通貨の数だけループする
        foreach($crypto_data as $key => &$data) {
            Log::debug('ループのdata:' . print_r($data, true));
            // 条件からツイート数を取得
            if ($data['complete_flg'] == false) {
                $data = TwitterController::countTweet($key, $start_day, $data); // TBD: メソッド未実装
            }

            // twitterの制限状態を確認(フラグが立っている場合、下ろしてループ抜ける)
            if (TwitterController::$searchd_limit_flg == true) {
                TwitterController::$searchd_limit_flg = false;
                break;
            }
        }
        return $crypto_data;
    }

    private function createCryptoData(array $list) {
        $cyrpto_data = array();

        foreach($list as $item) {
            $cyrpto_data[$item] = [
                'hour_cnt' => 0,
                'day_cnt' => 0,
                'week_cnt' => 0,
                'complete_flg' => false,
                'next_params' => ''
            ];
        }
        return $cyrpto_data;
    }
}
