<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Support\Facades\Log;


class TwitterController extends Controller
{
    const MAX_SEARCH = 450;

    private static $searchd_cnt = 0;
    public static $searchd_limit_flg = false;
    
    /**
     * 指定されたワードの検索数を１時間、１日、１週間でそれぞれ計測する。
     * @param string $word
     * @return array $tweet_cnt
     */
    public static function countTweet(string $word, string $ref_date, array $data) {
        // twitterAPIのパラメータ初期化
        $params = array();

        // 計測用のunixtime, date取得
        $ut_one_hour_ago = strtotime($ref_date . " -1 hour");
        $ut_a_day_ago = strtotime($ref_date . " -1 day");
        $string_since_day = date("Y-m-d_H:i:s", strtotime($ref_date . " -7 day"));

        // クライアントIDでツイッター認証
        $config = config('twitter');
        $connection = new TwitterOAuth($config['api_key'], $config['secret_key'], $config['access_token'], $config['access_token_secret']);
        
        // パラメータを設定
        if ($data['next_params']){
            parse_str($data['next_params'], $params);
        } else {
            $params = ['q' => $word . ' since:' . $string_since_day, 'count' => 100]; // 例：「BTC since:2019-09-09_00:00:00」で100件検索する
        }
        
        // 全件取得 or アクセス制限の上限までループ
        for($i=0; $i<self::MAX_SEARCH; $i++) {
            Log::debug('twitter接続開始:');
            Log::debug('twitter接続時のパラメータ:'. print_r($params, true));
            $result_std = $connection->get('search/tweets', $params);
            $result = json_decode(json_encode($result_std), true);
            // Log::debug('ツイートresult:'. print_r($result, true));

            // レスポンスがエラーで返ってきた場合、limit_flgを立ててループを抜ける
            if (isset($result['errors'])) {
                Log::debug('resultエラー:'. print_r($result['errors'], true));
                self::$searchd_limit_flg = true;
                self::$searchd_cnt = 0;
                break;
            }

            // 各期間毎にツイートを集計
            foreach ($result['statuses'] as $arr) {
                $ut_result = strtotime($arr['created_at']); // 取得データの日時をunixtimeに変換

                if ($ut_result >= $ut_one_hour_ago) {
                    $data['hour_cnt']++;
                    $data['day_cnt']++;
                    $data['week_cnt']++;
                } else if ($ut_result >= $ut_a_day_ago) {
                    $data['day_cnt']++;
                    $data['week_cnt']++;
                } else {
                    $data['week_cnt']++;
                }
            }

            // 全件取得できたか判定する
            if (!isset($result['search_metadata']['next_results'])) {
                $data['next_params'] = "";
                $data['complete_flg'] = true;
                break;
            } else {
                // 先頭の「?」を除去
                $next_results = preg_replace('/^\?/', '', $result['search_metadata']['next_results']);
                $data['next_params'] = $next_results;
            }

            // next_resultsから次の検索パラメータを設定
            parse_str($data['next_params'], $params);

            // search回数をカウント
            self::$searchd_cnt++;
            Log::debug('制限カウント：'.self::$searchd_cnt);
            if (self::$searchd_cnt >= self::MAX_SEARCH) {
                self::$searchd_limit_flg = true;
                self::$searchd_cnt = 0;
                break;
            }
        }

        Log::debug('twitterController集計結果:'. print_r($data, true));
        Log::debug('制限フラグ：'.self::$searchd_limit_flg);
        Log::debug('制限カウント：'.self::$searchd_cnt);
        return $data;
    }
}
