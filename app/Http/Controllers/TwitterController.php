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


    const MAX_TWEET_SEARCH = 450; // twitterAPIのツイート検索時の上限(450/15min)

    private static $searchd_tweet_cnt = 0; // ツイート検索数
    public static $searchd_tweet_limit_flg = false; // ツイート検索の上限フラグ
    

    /**
     * 指定されたワードの検索数を１時間、１日、１週間でそれぞれ計測する。
     * @param string $word, string $start_day, array $data, int $started_time
     * @return array $data
     */
    public static function countTweet(string $word, string $start_day, array $data, int $started_time) {
        // twitterAPIのパラメータ初期化
        $params = array();

        // 計測用のunixtime, date取得
        $ut_one_hour_ago = strtotime($start_day . " -1 hour"); // 検索開始時間の１時間前のunixtime
        $ut_a_day_ago = strtotime($start_day . " -1 day"); // 検索開始時間の１日前のunixtime
        $ut_a_week_ago = strtotime($start_day . " -1 week"); // 検索開始時間の１週間前のunixtime
        $string_since_day = date("Y-m-d_H:i:s", strtotime($start_day . " -7 day")); // 検索期間の最初の時刻
        $string_until_day = date("Y-m-d_H:i:s", strtotime($start_day)); // 検索期間の最後の時刻

        // アプリケーション認証
        $config = config('twitter');
        $twitter = new TwitterOAuth($config['api_key'], $config['secret_key']);
        $access_token = $twitter->oauth2('oauth2/token', ['grant_type' => 'client_credentials']);
        $connection = new TwitterOAuth($config['api_key'], $config['secret_key'], null, $access_token->access_token);
        $connection->setTimeouts(10, 10);
        
        // パラメータを設定
        if ($data['next_params']){
            parse_str($data['next_params'], $params);
        } else {
            $params = ['q' => $word . ' since:' . $string_since_day . '_JST', 'until' => $string_until_day . '_JST', 'count' => 100]; // 例：「BTC since:2019-09-09_00:00:00」で100件検索する
        }
        
        // 全件取得 or アクセス制限の上限までループ
        for($i=0; $i<self::MAX_TWEET_SEARCH; $i++) {
            // タイムアウトチェック
            $now = time();
            if($now - $started_time >= 600) {
                Log::debug('タイムアウト(countTweet):');
                self::$searchd_tweet_limit_flg = true;
                self::$searchd_tweet_cnt = 0;
                break;
            }

            // Log::debug('twitter接続時のパラメータ:'. print_r($params, true));
            $result_std = $connection->get('search/tweets', $params);
            $result = json_decode(json_encode($result_std), true);

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
            if (self::$searchd_tweet_cnt >= self::MAX_TWEET_SEARCH) {
                Log::debug('制限に引っかかりました。');
                Log::debug('制限カウント：'.self::$searchd_tweet_cnt);
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


    /**
     * キーワードに関連するユーザを取得し、返す。
     * @param int $next_page, string $search_word
     * @return array $users_arr
     */
    public static function searchTweetUsers(int $next_page, string $search_word) {
        Log::debug('searchTweetUsers(関数呼び出し)');
        // ユーザ情報の配列を初期化
        $users_arr = array();
        
        $config = config('twitter');
        $connection = new TwitterOAuth($config['api_key'], $config['secret_key'], $config['access_token'], $config['access_token_secret']);
        $connection->setTimeouts(10, 10);

        // パラメータを設定
        $params = ['q' => $search_word, 'page' => $next_page, 'count' => 20];

        for($i=0; $i<5; $i++) {
            Log::debug('パラメータ：'. print_r($params, true));

            if($params['page'] > 50) {
                Log::debug('取得件数最大:　page='. $params['page']);
                break;
            }

            $users = $connection->get('users/search', $params);
            // Log::debug('取得データ（apiの返り値）：'. print_r($users, true));

            // 取得データを連想配列に変換
            foreach($users as $user) {
                // Log::debug('個別データ：'. print_r($user, true));
                $user_arr = json_decode(json_encode($user), true);

                // レスポンスがエラーで返ってきた場合、処理を終了する
                if (isset($user_arr[0]['code'])) {
                    Log::debug('resultエラー:'. print_r($user_arr, true));
                        break 2;
                }

                // 鍵アカウントは対象外とする
                if ($user_arr['protected'] == true) {
                    continue;
                }

                // // 取得データに重複がないかチェック
                foreach($users_arr as $result) {
                    if($result['id_str'] === $user_arr['id_str']) { // 重複している場合、処理終了して上位に配列を返す
                        Log::debug('重複データあり(処理飛ばす)：');
                        Log::debug('重複データ($result["id_str"])：' . $result['id_str']);
                        Log::debug('重複データ($user_arr["id_str"])：' . $user_arr['id_str']);
                        continue 2; //　重複データは飛ばして、次のユーザの処理を実施
                    }
                }

                // 最新ツイートの埋め込みhtmlを取得
                $latest_html = "";
                if(!empty($user_arr['status'])) {
                    $latest_html = self::getOembed($connection, $user_arr['screen_name'], $user_arr['status']['id_str']);
                    if(empty($latest_html)) {
                        break 2;
                    }
                }
                $user_arr['latest_html'] = $latest_html;
                // Log::debug('latest_htmlの結果:'. $latest_html);

                // image_urlのサイズをオリジナルに変更する
                $pattern1 = '/_normal\./';
                $pattern2 = '/_normal$/';
                $replace1 = '.';
                $replace2 = '';
                $user_arr['profile_image_url'] = preg_replace($pattern1, $replace1, $user_arr['profile_image_url']);
                $user_arr['profile_image_url'] = preg_replace($pattern2, $replace2, $user_arr['profile_image_url']);

                // 返り値の配列にデータを追加
                $users_arr[] = $user_arr;
            }

            // パラメータを更新して次のユーザを検索
            $params['page'] ++;
        }
        $result_arr = ['users_arr' => $users_arr, 'next_page' =>$params['page']];
        return $result_arr;
    }


    /**
     * 引数からツイートの埋め込みhtmlを取得し、返す。
     * @param TwitterOAuth $connection, string $screen_name, int $id
     * @return string $html
     */
    public static function getOembed(TwitterOAuth $connection, string $screen_name, int $id) {
        // 取得したいツイートのurlを作成
        $url = 'https://twitter.com/' . $screen_name . '/status/' . $id;

        $params = ['url' => $url, 'maxwidth' => 284, 'omit_script' => true];
        $oembed = $connection->get('statuses/oembed', $params);
        if (empty($oembed->html)) {
            Log::debug('html取得失敗時の結果：' . print_r($oembed, true));
            $html = "";
            return $html;
        }
        $html = $oembed->html;

        return $html;
    }


    /**
     * リクエストトークンを発行し、ツイッター連携の確認画面の表示URLを返す。
     * 
     * @return redirect($url)
     */
    public static function authenticateUser() {
        Log::debug('authenticateUser(関数呼び出し)');

        // リクエストトークンを入手
        $config = config('twitter');
        // Log::debug('コールバックurl'.$config['call_back_url']);
        $connection = new TwitterOAuth($config['api_key'], $config['secret_key']);
        $connection->setTimeouts(10, 10);
        $request_token = $connection->oauth("oauth/request_token", array("oauth_callback" => $config['call_back_url']));

        // Log::debug('リクエストトークン'.$request_token['oauth_token']);
        // Log::debug('リクエストトークン(secret)'.$request_token['oauth_token_secret']);

        // callback後に認証で使用するため、セッションに保存
        session(['oauth_token' => $request_token['oauth_token']]);
        session(['oauth_token_secret' => $request_token['oauth_token_secret']]);

        // 認証画面へ移動
        $url = $connection->url('oauth/authorize', array(
            'oauth_token' => $request_token['oauth_token']
        ));
        // Log::debug('飛び先'.$url);

        return $url;
    }


    /**
     * アクセストークンを発行し、返す。
     * @param Request $request
     * @return array $access_token
     */
    public static function getAccessToken(Request $request) {
        Log::debug('callback後のリクエスト：'. $request);

        $oauth_token = session('oauth_token');
        $oauth_token_secret = session('oauth_token_secret');

        if( !($request->has('oauth_token')) ) {
            return null;
        } else if ($request->has('oauth_token') && $oauth_token !== $request->oauth_token) {
            return null;
        }

        // request_tokenからaccess_tokenを取得
        $connection = new TwitterOAuth(
            $oauth_token,
            $oauth_token_secret
        );
        $connection->setTimeouts(10, 10);
        $access_token = $connection->oauth('oauth/access_token', array(
            'oauth_verifier' => $request->oauth_verifier,
            'oauth_token' => $request->oauth_token,
        ));
        // Log::debug('アクセストークン：'. print_r($access_token, true));

        return $access_token;
    }


    /**
     * 対象アカウントをフォローする。
     * @param array $access_token, string $screen_name
     * @return void
     */
    public static function createFriendships(array $access_token, string $screen_name) {
        // ログインユーザにひもづくアクセストークンで認証する
        $config = config('twitter');
        $connection = new TwitterOAuth($config['api_key'], $config['secret_key'], $access_token['oauth_token'], $access_token['oauth_token_secret']);
        $connection->setTimeouts(10, 10);

        // 対象IDをフォローする
        $connection->post('friendships/create', array('screen_name' => $screen_name));
        return;
    }


    /**
     * 対象アカウントをフォロー解除する。
     * @param array $access_token, string $screen_name
     * @return void
     */
    public static function destroyFriendships(array $access_token, string $screen_name) {
        // ログインユーザにひもづくアクセストークンで認証する
        $config = config('twitter');
        $connection = new TwitterOAuth($config['api_key'], $config['secret_key'], $access_token['oauth_token'], $access_token['oauth_token_secret']);
        $connection->setTimeouts(10, 10);

        // 対象IDをフォローする
        $connection->post('friendships/destroy', array('screen_name' => $screen_name));
        return;
    }


    /**
     * ユーザと対象アカウントのフォロー状態を取得する
     * @param array $access_token, array $params
     * @return void
     */
    public static function lookupFriendships(array $access_token, array $params) {
        // ログインユーザにひもづくアクセストークンで認証する
        $config = config('twitter');
        $connection = new TwitterOAuth($config['api_key'], $config['secret_key'], $access_token['oauth_token'], $access_token['oauth_token_secret']);
        $connection->setTimeouts(10, 10);


        $result_std = $connection->get('friendships/lookup', $params);
        $result = json_decode(json_encode($result_std), true);
        // Log::debug('lookupFriendships取得データ(配列):' . print_r($result, true));

        // レスポンスがエラーで返ってきた場合、
        if (isset($result['errors'])) {
            Log::debug('resultエラー:'. print_r($result['errors'], true));
            return "";
        } else if (empty($result)) {
            Log::debug('resultエラー:返り値null');
            return "";
        }
        return $result;
    }
}
