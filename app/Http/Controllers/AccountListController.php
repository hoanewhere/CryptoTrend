<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\TwitterController;

//Model
use App\UpdatedTime;
use App\SearchedAccount;
use App\FollowManagement;
use App\TwitterFollowing;
use App\User;

class AccountListController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Account List Controller
    |--------------------------------------------------------------------------
    |
    | 仮想通貨のキーワードに関連するアカウントをtwitterから取得し、
    | DB保存やview側にデータを渡すコントローラ
    |
    */


    const MAX_FOLLOW_DAY_LIMIT = 1000; // twitterAPIの一日でのフォロー人数制限
    const USER_SEARCH_WORD = '仮想通貨'; // ユーザ検索時のキーワード

    
    /**
     * ツイッターアカウントとの連携を確認し、アカウント一覧画面を表示する。（一覧データ未取得の場合は取得処理も実施）
     * 
     * @return view 'アカウント一覧画面'
     */
    public function index() {
        // test
        // $this->saveFollowingData($login_user);
        // $this->getUsers();
        // $this->toFollowAutoLimit();
        // $this->saveFollowingDataAllUsers();

        return view('crypto.accountList');
    }


    /**
     * ツイッター連携でのコールバック時に呼ばれる処理(アクセストークンを取得、DB保存し、一覧画面に戻る)
     * 
     * @return view 'アカウント一覧画面'
     */
    public function callback(Request $request) {
        Log::debug('callback後のメソッド呼び出し');
        // アクセストークンを取得する
        $access_token = TwitterController::getAccessToken($request);
        if(empty($access_token)) {
            return redirect('accountList'); //TBD: 連携失敗時にインフォメーションだす
        }

        // アクセストークンをjson形式でDBに保存する
        $access_token_json = json_encode($access_token);
        $login_user = Auth::user();
        $login_user->fill([
            'access_token' => $access_token_json
        ]);
        $login_user->save();

        // 連携ユーザとのフォロー状態を確認して保存する
        $this->saveFollowingData($login_user);

        return redirect('accountList');
    }
    

    /**
     * ログインユーザにひもづくアカウントで仮想通貨に関連するユーザを取得し、DBに保存する。
     * 
     * @return void
     */
    public function getUsers() {
        Log::debug('getUsers(関数呼び出し)');

        $today = date('Y-m-d');
        $next_page = 1;
        $updated_time_result = UpdatedTime::where('time_index', '2')->where('created_at', 'LIKE', "$today%")->get();

        // データ取得状況確認
        if(count($updated_time_result)) {
            if($updated_time_result[0]->complete_flg) {
                return;
            } else {
                $next_page = $updated_time_result[0]->next_page;
            }
        } else {
            // UpdatedTimeテーブルにレコード追加
            $updated_time = New UpdatedTime();
            $updated_time->fill([
                'time_index' => 2,
                'complete_flg' => false,
            ]);
            $updated_time->save();
        }

        // ユーザ検索
        $result_arr = $this->searchUsers($next_page);
        $users = $result_arr['users_arr'];
        $next_page = $result_arr['next_page'];

        // ユーザデータ保存
        $this->saveUsers($users, $next_page);
    }


    /**
     * ログインユーザにひもづくアカウントをDBから読み出し、渡す。
     * 
     * @return array $res_data
     */
    public function reloadTweetData() {
        Log::debug('reloadTweetData(関数呼び出し)');
        $got_time = "";
        $accounts = array();
        $connected_twitter_flg = false;
        $auto_follow_flg = false;
        $login_flg = false;
        
        $login_user = Auth::user();
        if($login_user) {
            // ログインフラグON
            $login_flg = true;

            // twitter連携状態確認
            if(!empty($login_user->access_token)) {
                $connected_twitter_flg = true;
            }

            // 自動フォローフラグ取得
            $follow_management = FollowManagement::where('login_user_id', $login_user->id)->first();
            if(!empty($follow_management)) {
                $auto_follow_flg = $follow_management->auto_follow_flg;
            }
        }

        // 更新日付取得
        $updated_time = UpdatedTime::where('time_index', 2)->where('complete_flg', true)->orderby('created_at', 'desc')->first();

        // twitterユーザ取得
        if($updated_time) {
            if ($connected_twitter_flg) {
                $accounts = SearchedAccount::leftJoin('twitter_followings', 'searched_accounts.account_data->id_str', '=', 'twitter_followings.searched_twitter_id_str')
                                            ->where('update_time_id', $updated_time->id)
                                            ->where('twitter_followings.login_user_id', $login_user->id)
                                            ->orderby('twitter_followings.following', 'asc')->paginate(24);
            } else {
                $accounts = SearchedAccount::where('update_time_id', $updated_time->id)->paginate(24);
            }

            $got_time = 'データ取得時間：' . date("Y-m-d H:i:s", strtotime($updated_time->updated_at));
        } else {
            $got_time = "データ取得中";
        }

        $res_data = [
            'accounts' => $accounts,
            'got_time' => $got_time,
            'auto_follow_flg' => $auto_follow_flg,
            'connected_twitter_flg' => $connected_twitter_flg,
            'login_flg' => $login_flg,
        ];
    
        Log::debug('初期表示データ：' . print_r($res_data, true));
        return $res_data;
    }


    /**
     * 自動フォローフラグがONのユーザに対して、自動フォローを実施する。(15人/15minの制限あり)
     * 
     * @return void
     */
    public function toFollowAutoLimit() {
        Log::debug('toFollowAutoLimit(関数呼び出し)');

        // 自動フォローフラグONのユーザを取得
        $auto_flg_users = FollowManagement::where('auto_follow_flg', true)->get();
        
        foreach($auto_flg_users as $auto_flg_user) { // auto_flgが立っているuserに対して処理を実施
            // 未フォローのアカウントを15件取得
            $login_user_id = $auto_flg_user->login_user_id;
            $updated_time_id = UpdatedTime::select('id')->where('time_index', 2)->where('complete_flg', true)->latest()->first();
            $accounts = SearchedAccount::select('account_data->screen_name as screen_name', 'twitter_followings.id as following_id')
                                        ->leftJoin('twitter_followings', 'searched_accounts.account_data->id_str', '=', 'twitter_followings.searched_twitter_id_str')
                                        ->where('update_time_id', $updated_time_id->id)
                                        ->where('twitter_followings.login_user_id', $login_user_id)
                                        ->where('twitter_followings.following', false)->limit(15)->get();

            foreach($accounts as $account) { // 最大15人に対してフォロー処理を実施
                // day_cnt確認
                $follow_management = FollowManagement::where('login_user_id', $login_user_id)->first();
                $day_cnt = $follow_management->day_cnt;
                // Log::debug('アカウント名前: ' . print_r($account, true));
                if($day_cnt < self::MAX_FOLLOW_DAY_LIMIT) {
                    self::toFollowAuto($account->following_id, $account->screen_name, $login_user_id);

                    $follow_management->fill([
                        'day_cnt' => ++$day_cnt
                    ])->save();
                } else {
                    break;
                }
            }
        }
    }


    /**
     * ツイッターフォロー数の一日の制限数をクリアする。(1000人/1dayの制限あり)
     * 
     * @return void
     */
    public static function clearFollowCntOfDayLimit() {
        $follow_management = FollowManagement::where('id', '>=', 1)->update(['day_cnt' => 0]);
    }


    /**
     * 自動フォローでフォローしたユーザの内部データの情報（フォロー）を更新する。
     * @param int $record_id, string $screen_name, int $user_id
     * @return void
     */
    public static function toFollowAuto( int $record_id, string $screen_name, int $user_id) {
        Log::debug('toFollowAuto(関数呼び出し)'); 

        $user = User::where('id', $user_id)->first();
        $access_token = json_decode($user->access_token, true);
        TwitterController::createFriendships($access_token, $screen_name);

        //DB更新
        $twitter_following = TwitterFollowing::where('id', $record_id)->update(['following' => true]);
    }


    /**
     * ユーザ操作でフォローしたユーザの内部データの情報（フォロー）を更新する。
     * @param Request $request
     * @return void
     */
    public function toFollow( Request $request) {
        Log::debug('toFollow(関数呼び出し)');
        $request->validate([
            'record_id' => 'required|integer',
            'screen_name' => 'required|string'
        ]);
        // Log::debug('リクエスト(record_id)' . $request->record_id);
        // Log::debug('リクエスト(screen_name)' . $request->screen_name);

        $login_user = Auth::user();
        $access_token = json_decode($login_user->access_token, true);
        TwitterController::createFriendships($access_token, $request->screen_name);

        //DB更新
        $twitter_following = TwitterFollowing::where('id', $request->record_id)->update(['following' => true]);
    }


    /**
     * ユーザ操作でフォロー解除したユーザの内部データの情報（フォロー解除）を更新する。
     * @param Request $request
     * @return void
     */
    public function unfollow( Request $request) {
        Log::debug('unfollow(関数呼び出し)');
        $request->validate([
            'record_id' => 'required|integer',
            'screen_name' => 'required|string'
        ]);

        $login_user = Auth::user();
        $access_token = json_decode($login_user->access_token, true);
        TwitterController::destroyFriendships($access_token, $request->screen_name);

        //DB更新
        $twitter_following = TwitterFollowing::where('id', $request->record_id)->update(['following' => false]);
    }


    /**
     * 自動フォロー情報を制御する（追加、変更）。
     * @param Request $request
     * @return void
     */
    public function toggleAutoFollow( Request $request ) {
        Log::debug('toggleAutoFollow(関数呼び出し)');
        $request->validate([
            'auto_flg' => 'required|boolean',
        ]);

        $login_user = Auth::user();
        $follow_management = FollowManagement::updateOrCreate(
            ['login_user_id' => $login_user->id],
            ['auto_follow_flg' => $request->auto_flg]
        );
    }

    public function connectStart() {
        Log::debug('connect開始');
        $login_user = Auth::user();
        if(empty($login_user->access_token)) {
            // return redirect(TwitterController::authenticateUser());
            return TwitterController::authenticateUser();
        }
    }


    public function connectStop() {
        Log::debug('connect停止');
        $login_user = Auth::user();
        $follow_management = FollowManagement::where('login_user_id', $login_user->id)->first();
        $twitter_followings = TwitterFollowing::where('login_user_id', $login_user->id)->get();
        if(!empty($login_user->access_token)) {
            $login_user->fill([
                'access_token' => null,
            ]);
            $login_user->save();

            if(!empty($follow_management)) {
                $follow_management->fill([
                    'auto_follow_flg' => false,
                ]);
                $follow_management->save();
            }
            
            if(!empty($twitter_followings)) {
                foreach($twitter_followings as $twitter_following) {
                    $twitter_following->delete();
                }
            }
        }
        // データ再取得
        $this->reloadTweetData();
    }


    public function saveFollowingData(User $user) {
        Log::debug('saveFollowingData(関数呼び出し)');
        // パラメータ初期化
        $params['user_id'] = '';
        $cnt = 0;

        // $login_user = Auth::user();
        $access_token = json_decode($user->access_token, true);
        $updated_time = UpdatedTime::where('time_index', 2)->where('complete_flg', true)->orderby('created_at', 'desc')->first();
        $searched_accounts = SearchedAccount::select('id', 'account_data->id_str as twitter_id_str')->where('update_time_id', $updated_time->id)->get();
        foreach($searched_accounts as $searched_account) {
            $cnt++;
            Log::debug('friendsips/lokupの単品パラメータ:' . $searched_account->twitter_id_str);
            $params['user_id'] = $params['user_id'] . ($searched_account->twitter_id_str) . ',';
            Log::debug('friendsips/lokupのパラメータ:' . $params['user_id']);
            
            if($cnt == 100) { // パラメータに１００件のidが貯まったら以下処理実施
                $this->saveFollowingDataDetail($user->id, $params, $access_token);
                $cnt = 0;
                $params['user_id'] = '';
            }
        }
        if($cnt !== 0) { // 100件ずつのループで最後に溢れたIDに対しての処理
            $this->saveFollowingDataDetail($user->id, $params, $access_token);
        }
    }


    public function saveFollowingDataAllUsers() {
        Log::debug('saveFollowingDataAllUsers(関数呼び出し)');
        $targetUsers = User::whereNotNull('access_token')->get();
        
        foreach($targetUsers as $targetUser) {
            $this->saveFollowingData($targetUser);
        }
    }

    // **
    // 以下 private関数
    // **

    /**
     * 仮想通貨に関連するユーザを取得する。
     * @param int $next_page
     * @return array $result_arr
     */
    private function searchUsers(int $next_page) {
        Log::debug('serachUsers(関数呼び出し)');

        $result_arr = TwitterController::searchTweetUsers($next_page, self::USER_SEARCH_WORD);
        // Log::debug('取得データ(result_arr)：'. print_r($result_arr, true));
        return $result_arr;
    }

    
    /**
     * 仮想通貨に関連するユーザをDBに保存する。
     * 
     * @param array $users, int $next_page
     * @return void
     */
    private function saveUsers(array $users, int $next_page) {
        Log::debug('saveUsers(関数呼び出し)');

        $complete_flg = false;
        $today = date('Y-m-d');
        $updated_time = UpdatedTime::where('time_index', '2')->where('created_at', 'LIKE', "$today%")->first(); 
        $saved_users = SearchedAccount::where('update_time_id', $updated_time->id)->get();

        foreach($users as $user) {
            // 重複チェック
            foreach($saved_users as $saved_user) {
                if($saved_user->id === $user['id']) { //tbd:$saved_user->account_data['id']じゃない？　あと比較するならid_strでする
                    Log::debug('重複ありのため終了');
                    $complete_flg = true;
                    $next_page = 0;
                    break 2;
                }
            }

            // 重複していなければDBに保存する
            $user_json = json_encode($user);
            $searched_account = New SearchedAccount();
            $searched_account->fill([
                'account_data' => $user_json,
                'update_time_id' => $updated_time->id
            ]);
            $searched_account->save();
        }

        // 完了確認
        if($next_page > 50) {
            $complete_flg = true;
            $next_page = 0;
        }

        // timeテーブルを更新
        $updated_time->fill([
            'complete_flg' => $complete_flg,
            'next_page' => $next_page
        ]);
        $updated_time->save();
    }

    private function saveFollowingDataDetail (int $login_user_id, array $params, array $access_token) {
        // twitterapi friendships/lookup実施
        $following_states = TwitterController::lookupFriendships($access_token, $params);

        if($following_states == "") { // lookupFriendships処理が失敗した場合、何もしない（表示時の処理でフォロー状態をマスクする）
            return;
        }

        foreach($following_states as $state) {
            $following = false;
            if($state['connections'][0] == 'following') {
                $following = true;
            }
            $twitter_following = TwitterFollowing::updateOrCreate(
                ['login_user_id' => $login_user_id, 'searched_twitter_id_str' => $state['id_str']],
                ['following' => $following]
            );
        }
    }
}