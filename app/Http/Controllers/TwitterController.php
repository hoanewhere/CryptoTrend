<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Support\Facades\Log;


class TwitterController extends Controller
{
    const MAX_TWEET_SEARCH = 450;
    const MAX_USER_SEARCH = 900;

    private static $searchd_tweet_cnt = 0;
    public static $searchd_tweet_limit_flg = false;
    
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

        $config = config('twitter');

        // クライアントIDでツイッター認証
        // $connection = new TwitterOAuth($config['api_key'], $config['secret_key'], $config['access_token'], $config['access_token_secret']);

        // アプリケーション認証
        $twitter = new TwitterOAuth($config['api_key'], $config['secret_key']);
        $access_token = $twitter->oauth2('oauth2/token', ['grant_type' => 'client_credentials']);
        $connection = new TwitterOAuth($config['api_key'], $config['secret_key'], null, $access_token->access_token);
        
        // パラメータを設定
        if ($data['next_params']){
            parse_str($data['next_params'], $params);
        } else {
            $params = ['q' => $word . ' since:' . $string_since_day, 'count' => 100]; // 例：「BTC since:2019-09-09_00:00:00」で100件検索する
        }
        
        // 全件取得 or アクセス制限の上限までループ
        for($i=0; $i<self::MAX_TWEET_SEARCH; $i++) {
            Log::debug('twitter接続開始:');
            Log::debug('twitter接続時のパラメータ:'. print_r($params, true));
            $result_std = $connection->get('search/tweets', $params);
            $result = json_decode(json_encode($result_std), true);
            // Log::debug('ツイートresult:'. print_r($result, true));

            // レスポンスがエラーで返ってきた場合、limit_flgを立ててループを抜ける
            if (isset($result['errors'])) {
                Log::debug('resultエラー:'. print_r($result['errors'], true));
                self::$searchd_tweet_limit_flg = true;
                self::$searchd_tweet_cnt = 0;
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
            self::$searchd_tweet_cnt++;
            Log::debug('制限カウント：'.self::$searchd_tweet_cnt);
            if (self::$searchd_tweet_cnt >= self::MAX_TWEET_SEARCH) {
                self::$searchd_tweet_limit_flg = true;
                self::$searchd_tweet_cnt = 0;
                break;
            }
        }

        Log::debug('twitterController集計結果:'. print_r($data, true));
        Log::debug('制限フラグ：'.self::$searchd_tweet_limit_flg);
        Log::debug('制限カウント：'.self::$searchd_tweet_cnt);
        return $data;
    }

    
    public static function searchTweetUsers(string $access_tokun, string $access_secret) {
        // 返り値の配列を初期化
        $result_arr = array();
        
        // ユーザログインのアカウント認証
        // TBD:引数にあるユーザログインにひもづくアクセストークンで認証する。（現状はクライアントで認証）
        $config = config('twitter');
        $connection = new TwitterOAuth($config['api_key'], $config['secret_key'], $config['access_token'], $config['access_token_secret']);

        // パラメータを設定
        $params = ['q' => '憚る', 'page' => 0, 'count' => 20];

        for($i=1; $i<=50; $i++) {
            // パラメータを更新してユーザを検索
            $params['page'] = $i;
            Log::debug('パラメータ：'. print_r($params, true));
            $users = $connection->get('users/search', $params);
            Log::debug('取得データ（apiの返り値）：'. print_r($users, true));

            // 取得データを連想配列に変換
            foreach($users as $user) {
                Log::debug('個別データ：'. print_r($user, true));
                $user_arr = json_decode(json_encode($user), true);

                // レスポンスがエラーで返ってきた場合、処理を終了する
                if (isset($user_arr[0]['code'])) {
                Log::debug('resultエラー:'. print_r($user_arr, true));
                return $result_arr;
                }

                // 取得データに重複がないかチェック
                foreach($result_arr as $result) {
                    if($result['id'] === $user_arr['id']) { // 重複している場合、処理終了して上位に配列を返す
                        Log::debug('重複データあり(処理終了)：');
                        return $result_arr;
                    }
                }

                // 重複してなければ返り値の配列に取得データを追加
                $result_arr[] = $user_arr;
            }
        }

        return $result_arr;
    }
}
