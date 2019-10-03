<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Support\Facades\Log;


class TwitterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Twitter Controller
    |--------------------------------------------------------------------------
    |
    | Twitter APIにアクセスするコントローラ
    |
    */

    const MAX_TWEET_SEARCH = 450;
    const MAX_USER_SEARCH = 900;

    private static $searchd_tweet_cnt = 0;
    public static $searchd_tweet_limit_flg = false;
    
    /**
     * 指定されたワードの検索数を１時間、１日、１週間でそれぞれ計測する。
     * @param string $word
     * @return array $tweet_cnt
     */
    public static function countTweet(string $word, string $start_day, array $data) {
        // twitterAPIのパラメータ初期化
        $params = array();

        // 計測用のunixtime, date取得
        $ut_a_week_ago = strtotime($start_day . " -1 week"); // 検索開始時間の１週間前のunixtime

        // アプリケーション認証
        $twitter = new TwitterOAuth($config['api_key'], $config['secret_key']);
        $access_token = $twitter->oauth2('oauth2/token', ['grant_type' => 'client_credentials']);
        $connection = new TwitterOAuth($config['api_key'], $config['secret_key'], null, $access_token->access_token);
        
        // パラメータを設定
        if ($data['next_params']){
            parse_str($data['next_params'], $params);
        } else {
            $params = ['q' => $word . ' since:' . $string_since_day . '_JST', 'until' => $string_until_day . '_JST', 'count' => 100]; // 例：「BTC since:2019-09-09_00:00:00」で100件検索する
        }
        
        // 全件取得 or アクセス制限の上限までループ
        for($i=0; $i<self::MAX_TWEET_SEARCH; $i++) {
            // Log::debug('twitter接続開始:');
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

                // Log::debug('ut_result(取得データの生成時間):'. $ut_result);

                if ($ut_result >= $ut_one_hour_ago) {
                    // Log::debug('ut_one_hour_ago(１時間前の時間):'. $ut_one_hour_ago);
                    $data['hour_cnt']++;
                    $data['day_cnt']++;
                    $data['week_cnt']++;
                } else if ($ut_result >= $ut_a_day_ago) {
                    // Log::debug('ut_a_day_ago(１日前の時間):'. $ut_a_day_ago);
                    $data['day_cnt']++;
                    $data['week_cnt']++;
                } else if ($ut_result >= $ut_a_week_ago) {
                    // Log::debug('ut_a_week_ago(１週間前の時間):'. $ut_a_week_ago);
                    $data['week_cnt']++;
                } else {
                    // 何もしない
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

    
    public static function searchTweetUsers(array $access_token) {
        // 返り値の配列を初期化
        $result_arr = array();
        
        // ログインユーザにひもづくアクセストークンで認証する
        $config = config('twitter');
        $connection = new TwitterOAuth($config['api_key'], $config['secret_key'], $access_token['oauth_token'], $access_token['oauth_token_secret']);

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

                // 重複していなければ最新ツイートの埋め込みhtmlを取得
                $latest_html = "";
                if(!empty($user_arr['status'])) {
                    $latest_html = self::getOembed($connection, $user_arr['screen_name'], $user_arr['status']['id']);
                }
                $user_arr['latest_html'] = $latest_html;
                Log::debug('latest_htmlの結果:'. $latest_html);

                // image_urlのサイズをオリジナルに変更する
                $pattern1 = '/_normal\./';
                $pattern2 = '/_normal$/';
                $replace1 = '.';
                $replace2 = '';
                $user_arr['profile_image_url'] = preg_replace($pattern1, $replace1, $user_arr['profile_image_url']);
                $user_arr['profile_image_url'] = preg_replace($pattern2, $replace2, $user_arr['profile_image_url']);

                // 返り値の配列にデータを追加
                $result_arr[] = $user_arr;
            }
        }

        return $result_arr;
    }


    public static function getOembed(TwitterOAuth $connection, string $screen_name, int $id) {
        // 取得したいツイートのurlを作成
        $url = 'https://twitter.com/' . $screen_name . '/status/' . $id;

        $params = ['url' => $url, 'maxwidth' => 284, 'omit_script' => true];
        $oembed = $connection->get('statuses/oembed', $params);
        $html = $oembed->html;

        Log::debug('$oembedの結果:'. print_r($oembed, true));
        Log::debug('htmlは？:'. $html);

        return $html;
    }


    public static function authenticateUser() {
        Log::debug('authenticateUser(関数呼び出し)');

        // リクエストトークンを入手
        $config = config('twitter');
        Log::debug('コールバックurl'.$config['call_back_url']);
        $connection = new TwitterOAuth($config['api_key'], $config['secret_key']);
        $request_token = $connection->oauth("oauth/request_token", array("oauth_callback" => $config['call_back_url']));

        Log::debug('リクエストトークン'.$request_token['oauth_token']);
        Log::debug('リクエストトークン(secret)'.$request_token['oauth_token_secret']);

        // callback後に認証で使用するため、セッションに保存
        session(['oauth_token' => $request_token['oauth_token']]);
        session(['oauth_token_secret' => $request_token['oauth_token_secret']]);

        // 認証画面へ移動
        $url = $connection->url('oauth/authorize', array(
            'oauth_token' => $request_token['oauth_token']
        ));
        Log::debug('飛び先'.$url);

        return redirect($url);
    }

    public static function getAccessToken(Request $request) {
        Log::debug('callback後のリクエスト：'. $request);

        $oauth_token = session('oauth_token');
        $oauth_token_secret = session('oauth_token_secret');

        if ($request->has('oauth_token') && $oauth_token !== $request->oauth_token) {
            return null;
        }

        // request_tokenからaccess_tokenを取得
        $connection = new TwitterOAuth(
            $oauth_token,
            $oauth_token_secret
        );
        $access_token = $connection->oauth('oauth/access_token', array(
            'oauth_verifier' => $request->oauth_verifier,
            'oauth_token' => $request->oauth_token,
        ));
        Log::debug('アクセストークン：'. print_r($access_token, true));

        return $access_token;
    }

    public static function createFriendships(array $access_token, string $screen_name) {
        // ログインユーザにひもづくアクセストークンで認証する
        $config = config('twitter');
        $connection = new TwitterOAuth($config['api_key'], $config['secret_key'], $access_token['oauth_token'], $access_token['oauth_token_secret']);

        // 対象IDをフォローする
        $connection->post('friendships/create', array('screen_name' => $screen_name));
        return;
    }

    public static function destroyFriendships(array $access_token, string $screen_name) {
        // ログインユーザにひもづくアクセストークンで認証する
        $config = config('twitter');
        $connection = new TwitterOAuth($config['api_key'], $config['secret_key'], $access_token['oauth_token'], $access_token['oauth_token_secret']);

        // 対象IDをフォローする
        $connection->post('friendships/destroy', array('screen_name' => $screen_name));
        return;
    }
}
