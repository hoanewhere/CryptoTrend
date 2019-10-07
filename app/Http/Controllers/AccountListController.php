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


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * ツイッターアカウントとの連携を確認し、アカウント一覧画面を表示する。（一覧データ未取得の場合は取得処理も実施）
     * 
     * @return view 'アカウント一覧画面'
     */
    public function index() {
        $login_user = Auth::user();
        if(empty($login_user->access_token)) {
            return TwitterController::authenticateUser();
        }

        // アカウント取得実施(既に実施している場合は何もしない)
        $this->getUsers();

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
            return redirect('index');
        }

        // アクセストークンをjson形式でDBに保存する
        $access_token_json = json_encode($access_token);
        $login_user = Auth::user();
        $login_user->fill([
            'access_token' => $access_token_json
        ]);
        $login_user->save();

        $this->getUsers();
        return redirect('accountList');
    }
    

    /**
     * ログインユーザにひもづくアカウントで仮想通貨に関連するユーザを取得し、DBに保存する。
     * 
     * @return void
     */
    public function getUsers() {
        Log::debug('getUsers(関数呼び出し)');

        // 今日、すでにgetUsers()が実施されている場合は何もしない
        $today = date('Y-m-d');
        $updated_time_result = UpdatedTime::where('time_index', '2')->where('created_at', 'LIKE', "$today%")->get(); 
        if(count($updated_time_result)) {
            if($updated_time_result[0]->complete_flg) {
                return;
            }
        }

        $login_user = Auth::user();
        $login_user_id = $login_user->id;
        $access_token = json_decode($login_user->access_token, true);

        // UpdatedTimeテーブルにレコード追加
        $updated_time = New UpdatedTime();
        $updated_time->fill([
            'time_index' => 2,
            'complete_flg' => false,
            'login_user_id' => $login_user_id
        ]);
        $updated_time->save();

        // ユーザ検索
        $users = $this->searchUsers($access_token);

        // ユーザデータ保存
        $this->saveUsers($users, $login_user_id);
    }


    /**
     * ツイッター連携してる全ユーザに対して仮想通貨に関連するユーザを取得し、DBに保存する。
     * 
     * @return void
     */
    public function getUsersAllAcounts() {
        Log::debug('getUsersAllAcounts(関数呼び出し)');
        $today = date('Y-m-d');
        $targetUsers = User::whereNotNull('access_token')->get();
        
        foreach($targetUsers as $targetUser) {
            $updated_time_result = UpdatedTime::where('time_index', '2')->where('login_user_id', $targetUser->id)->where('created_at', 'LIKE', "$today%")->get(); 

            // 今日、すでにアカウント取得が実施されているユーザに対しては何もしない
            if(count($updated_time_result)) {
                if($updated_time_result[0]->complete_flg) {
                    continue;
                }
            }

            $user_id = $targetUser->id;
            $access_token = json_decode($targetUser->access_token, true);

            // UpdatedTimeテーブルにレコード追加
            $updated_time = New UpdatedTime();
            $updated_time->fill([
                'time_index' => 2,
                'complete_flg' => false,
                'login_user_id' => $user_id
            ]);
            $updated_time->save();

            // ユーザ検索
            $users = $this->searchUsers($access_token);

            // ユーザデータ保存
            $this->saveUsers($users, $user_id);
        }
    }


    /**
     * ログインユーザにひもづくアカウントをDBから読み出し、渡す。
     * 
     * @return array $res_data
     */
    public function reloadTweetData() {
        Log::debug('reloadTweetData(関数呼び出し)');
        $login_user = Auth::user();

        // 更新日付取得
        $updated_time = UpdatedTime::where('time_index', 2)->where('complete_flg', true)->where('login_user_id', $login_user->id)->orderby('created_at', 'desc')->first();

        // アカウント取得
        $accounts = SearchedAccount::select(['id', 'account_data'])->where('update_time_id', $updated_time->id)->orderby('account_data->following', 'asc')->paginate(2);
        // Log::debug('未アカウントフォロー:'. print_r($accounts[0]->account_data, true));

        // 自動フォローフラグ取得
        $auto_follow_flg = false;
        $follow_management = FollowManagement::where('login_user_id', $login_user->id)->first();
        if(!empty($follow_management)) {
            $auto_follow_flg = $follow_management->auto_follow_flg;
        }

        $res_data = [
            'accounts' => $accounts,
            'got_time' => date("Y-m-d H:i:s", strtotime($updated_time->created_at)),
            'auto_follow_flg' => $auto_follow_flg
        ];
    
        // Log::debug('初期表示データ：' . print_r($res_data, true));
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
            $updated_time_id = UpdatedTime::select('id')->where('login_user_id', $login_user_id)->where('time_index', 2)->where('complete_flg', true)->latest()->first();
            $accounts = SearchedAccount::select('id', 'account_data->screen_name as screen_name')->where('update_time_id', $updated_time_id->id)->where('account_data->following', false)->limit(15)->get();

            foreach($accounts as $account) { // 最大15人に対してフォロー処理を実施
                // day_cnt確認
                $follow_management = FollowManagement::where('login_user_id', $login_user_id)->first();
                $day_cnt = $follow_management->day_cnt;
                Log::debug('アカウント名前: ' . print_r($account, true));
                if($day_cnt < self::MAX_FOLLOW_DAY_LIMIT) {
                    self::toFollowAuto($account->id, $account->screen_name, $login_user_id);

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
        $searched_account = SearchedAccount::where('id', $record_id)->update(['account_data->following' => true]);
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
        $searched_account = SearchedAccount::where('id', $request->record_id)->update(['account_data->following' => true]);
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
        $searched_account = SearchedAccount::where('id', $request->record_id)->update(['account_data->following' => false]);
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


    // **
    // 以下 private関数
    // **

    /**
     * 仮想通貨に関連するユーザを取得する。
     * @param array $access_token
     * @return array $result_arr
     */
    private function searchUsers(array $access_token) {
        Log::debug('serachUsers(関数呼び出し)');

        $result_arr = TwitterController::searchTweetUsers($access_token);
        Log::debug('取得データ(result_arr)：'. print_r($result_arr, true));
        Log::debug('取得データの数：'. count($result_arr));
        return $result_arr;
    }

    
    /**
     * 仮想通貨に関連するユーザをDBに保存する。
     * 
     * @param array $users, int $login_id
     * @return void
     */
    private function saveUsers(array $users, int $login_id) {
        Log::debug('saveUsers(関数呼び出し)');

        $today = date('Y-m-d');
        $updated_time = UpdatedTime::where('time_index', '2')->where('login_user_id', $login_id)->where('created_at', 'LIKE', "$today%")->get(); 

        foreach($users as $user) {
            $user_json = json_encode($user);
            $searched_account = New SearchedAccount();
            $searched_account->fill([
                'account_data' => $user_json,
                'update_time_id' => $updated_time[0]->id,
                'login_user_id' => $login_id
            ]);
            $searched_account->save();
        }

        // timeテーブルの完了フラグを更新
        $updated_time[0]->fill([
            'complete_flg' => true
        ]);
        $updated_time[0]->save();
    }
}