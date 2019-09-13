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
    public static function countTweet(string $word, string $since, array $data) {
        // 返り値の初期化
        $tweet_cnt = ['hour_cnt' => 0, 'day_cnt' => 0, 'week_cnt' => 0, 'error_flg' => False];

        // unixtime取得
        $ut_one_hour_ago = strtotime("-1 hour");
        $ut_a_day_ago = strtotime("-1 day");

        // クライアントIDでツイッター認証
        $config = config('twitter');
        $connection = new TwitterOAuth($config['api_key'], $config['secret_key'], $config['access_token'], $config['access_token_secret']);
        
        // 初回のパラメータを設定
        $params = ['q' => '老獪 since:' . '2019-09-03', 'count' => 100];

        // 全件取得できるまでループ
        while(1) {
            $result_std = $connection->get('search/tweets', $params);
            $result = json_decode(json_encode($result_std), true);
            Log::debug('ツイートresult:'. print_r($result, true));

            // レスポンスがエラーで返ってきた場合、error_flgを立てる
            if (isset($result['errors'])) {
                Log::debug('resultエラー:'. print_r($result['errors'], true));
                $tweet_cnt['error_flg'] = true;
                break;
            }

            // 各期間毎にツイートを集計
            foreach ($result['statuses'] as $arr) {
                $ut_result = strtotime($arr['created_at']); // 取得データの日時をunixtimeに変換

                if ($ut_result >= $ut_one_hour_ago) {
                    $tweet_cnt['hour_cnt']++;
                    $tweet_cnt['day_cnt']++;
                    $tweet_cnt['week_cnt']++;
                } else if ($ut_result >= $ut_a_day_ago) {
                    $tweet_cnt['day_cnt']++;
                    $tweet_cnt['week_cnt']++;
                } else {
                    $tweet_cnt['week_cnt']++;
                }
            }

            // 全件取得できたか判定する
            if (!isset($result['search_metadata']['next_results'])) {
                break;
            } else {
                // 先頭の「?」を除去
                $next_results = preg_replace('/^\?/', '', $result['search_metadata']['next_results']);
            }

            // next_resultsから次の検索パラメータを設定
            parse_str($next_results, $params);
        }

        Log::debug('ツイート件数(結果):'. print_r($tweet_cnt, true));
        return $tweet_cnt;
    }
}
