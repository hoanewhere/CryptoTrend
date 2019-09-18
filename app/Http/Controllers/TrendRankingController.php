<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\TwitterController;
use App\Http\Controllers\CoinCheckController;

//Model
use App\UpdatedTime;
use App\Trend;
use App\Crypto;

class TrendRankingController extends Controller
{
    public function index() {
        // $hogehoge =  $this->searchTrends(1);
        // Log::debug('$trends: '.print_r($hogehoge, true));

        // $this->aggregateTweetTrend();
        // $this->aggregateCryptoPrice();
        return view('crypto.trendRanking');
    }


    /**
     * 仮想通貨の取引価格（24時間での最大、最小額）を集計してDB更新する。
     * 
     * @return void
     */
    public function aggregateCryptoPrice(int $id) {
        // 現状はBTCのみの価格を取得
        $result = CoinCheckController::getTransactionPrice();
        
        // 取得結果からDB更新(BTCのみ)
        $time_id = $id;
        $trend = Trend::where('crypto_id', 1)->where('time_id', $time_id)->get();
        $trend[0]->fill([
            'transaction_price_max' => $result['high'],
            'transaction_price_min' => $result['low']
        ]);
        $trend[0]->save();
    }



    /**
     * トレンドワードのツイート数を集計してDB更新する。
     * 
     * @return void
     */
    public function aggregateTweetTrend() {
        Log::debug('aggregateTweetTrend(関数呼び出し)');

        $time_flg = false; // UpdatedTimeテーブルレコード有無フラグ
        $complete_flg = false; // UpdatedTimeテーブル完了フラグ
        $updated_time_result = array(); // UpdatedTimeテーブル呼び出し結果

        // 現在の日付でtimeテーブル呼び出し
        $today = date('Y-m-d');
        $updated_time_result = UpdatedTime::where('time_index', '1')->where('created_at', 'LIKE', "$today%")->get(); 
        if(count($updated_time_result)) {
            $time_flg = true;
            if($updated_time_result[0]->complete_flg) {
                $complete_flg = true;
            }
        }
        Log::debug('タイムテーブルの取得データ: '.print_r($updated_time_result, true));
        Log::debug('タイムフラグ: '.$time_flg);
        Log::debug('タイムテーブルのコンプリートフラグ: '.$complete_flg);

        if($time_flg) { //現在日付のレコードがある場合
            if ($complete_flg) { // 現在日付の情報取得が完了している場合
                return;
            } else { // 現在日付の情報収集が未完了の場合
                $start_day = date("Y-m-d H:i:s", strtotime($updated_time_result[0]->created_at)); //timestampからstringに変換。右記参照 $start_day = date('Y-m-d_H:i:s', strtotime(timeテーブルのtimestamp));
                $start_id = $updated_time_result[0]->id;

                // 集計データを取得
                $crypto_data = $this->resumeSearch($start_day, $start_id);
            }
        } else { //現在日付のレコードがない場合
            // UpdatedTimeテーブルにレコード追加
            $updated_time = New UpdatedTime();
            $updated_time->fill([
                'time_index' => 1,
                'complete_flg' => false
            ]);
            $updated_time->save();
            
            // UpdatedTimeテーブルに追加したレコードの時刻を読み出し
            $updated_time_result = UpdatedTime::where('time_index', '1')->where('created_at', 'LIKE', "$today%")->get(); 
            // Log::debug('タイムテーブルの取得データ: '.print_r($updated_time_result, true));
            $start_day = date("Y-m-d H:i:s", strtotime($updated_time_result[0]->created_at));
            $start_id = $updated_time_result[0]->id;

            // 初回の集計データを取得
            $crypto_data = $this->startSearch($start_day, $start_id);
        }

        // 集計データをもとにDB更新
        foreach($crypto_data as $key => $data) {
            $trend = Trend::where('crypto_id', $key)->where('time_id', $updated_time_result[0]->id)->get();
            $trend[0]->fill([
                'hour_cnt' => $data['hour_cnt'],
                'day_cnt' => $data['day_cnt'],
                'week_cnt' => $data['week_cnt'],
                'complete_flg' =>$data['complete_flg'],
                'next_params' =>$data['next_params'],
            ]);
            $trend[0]->save();
        }
        // Log::debug('集計結果: '.print_r($crypto_data, true));

        // 集計が完了したか確認する
        foreach($crypto_data as $item) {
            if($item['complete_flg'] == false) {
                Log::debug('集計完了(１５分後に再開):');
                return; // 一つでも未完了のものがあれば処理終了。15分後に処理再開。
            }
        }
        // 全て完了の場合、timeテーブルのcomplete_flgを完了にして処理終了
        $updated_time_result[0]->fill([
            'complete_flg' => true
        ]);
        $updated_time_result[0]->save();
        Log::debug('集計完了:');
    }


    /**
     * 初回の集計データを取得する。
     * @param string $start_day, int $start_id
     * @return array $cyrpto_data
     */
    private function startSearch(string $start_day, int $start_id) {
        Log::debug('startSearch(関数呼び出し)');

         // crypto_data作成
        $crypto_data = $this->createCryptoData();
        // Log::debug('作成データ: '.print_r($crypto_data, true));

        // 作成したデータでtrendテーブルにレコードを新規追加する
        foreach($crypto_data as $key => $data) {
            // Log::debug('Key: '. $key);
            // Log::debug('Data: '. print_r($data, true));
            $trend = New Trend();
            $trend->fill([
                'crypto_id' => $key,
                'complete_flg' => false,
                'next_params' => "",
                'time_id' => $start_id
            ]);
            $trend->save();
        }

        // 初回のタイミングで仮想通貨の取引価格を取得し、DB更新する
        $this->aggregateCryptoPrice($start_id);

        // 集計開始
        $crypto_data = $this->searchForDetails($start_day, $crypto_data);
        return $crypto_data;
    }

    /**
     * 前回の続きから集計データを取得する（前回のデータはDBから読み出す。）。
     * @param string $start_day, int $start_id
     * @return array $cyrpto_data
     */
    private function resumeSearch(string $start_day, int $start_id) {
        Log::debug('resumeSearch(関数呼び出し)');
        $crypto_data = array();

        // DBからデータ取得して$crypto_data作成
        $trends = Trend::where('time_id', $start_id)->get();
        foreach($trends as $trend) {
            $crypto_data[$trend->crypto_id] = [
                'hour_cnt' => $trend->hour_cnt,
                'day_cnt' => $trend->day_cnt,
                'week_cnt' => $trend->week_cnt,
                'complete_flg' => $trend->complete_flg,
                'next_params' => $trend->next_params,
            ];
        }
        
        // 集計開始
        $crypto_data = $this->searchForDetails($start_day, $crypto_data);
        return $crypto_data;
    }


    /**
     * 集計処理を呼び出して、集計結果を返す。
     * @param string $start_day, array $crypto_data
     * @return array $cyrpto_data
     */
    private function searchForDetails(string $start_day, array $crypto_data){
        Log::debug('searchForDetails(関数呼び出し)');
        Log::debug('引数:start_day:' . $start_day);
        // Log::debug('引数:crypto_data:' . print_r($crypto_data, true));

        // 仮想通貨の数だけループする
        foreach($crypto_data as $key => &$data) {
            // Log::debug('ループのdata:' . print_r($data, true));

            // 検索wordを取得
            $crypto_result = Crypto::where('id', $key)->get();
            $word = $crypto_result[0]->crypto;

            // 集計が完了していない単語のツイート数を取得
            if ($data['complete_flg'] == false) {
                $data = TwitterController::countTweet($word, $start_day, $data);
            }

            // twitterの制限状態を確認(フラグが立っている場合、下ろしてループ抜ける)
            if (TwitterController::$searchd_limit_flg == true) {
                TwitterController::$searchd_limit_flg = false;
                break;
            }
        }
        return $crypto_data;
    }


    /**
     * 初回の集計用データを作成する。
     *
     * @return array $cyrpto_data
     */
    private function createCryptoData() {
        $cyrpto_data = array();
        $crypto_list = [];

        $cryptos = Crypto::get();
        foreach($cryptos as $crypto) {
            $crypto_list[] = $crypto->id;
        }

        foreach($crypto_list as $item) {
            $cyrpto_data[$item] = [
                'hour_cnt' => 0,
                'day_cnt' => 0,
                'week_cnt' => 0,
                'complete_flg' => false,
                'next_params' => '',
            ];
        }
        return $cyrpto_data;
    }
}
