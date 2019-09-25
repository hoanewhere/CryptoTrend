<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\TwitterController;

//Model
use App\UpdatedTime;
use App\SearchedAccount;

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

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index() {
        $login_user = Auth::user();
        if(empty($login_user->access_token)) {
            return TwitterController::authenticateUser();
        }

        $this->getUsers();
        return view('crypto.accountList');
    }

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
     * 仮想通貨に関連するユーザを取得し、DBに保存する。
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


    public function reloadTweetData() {
        Log::debug('reloadTweetData(関数呼び出し)');
        $login_user = Auth::user();

        // 更新日付取得
        $updated_time = UpdatedTime::where('time_index', 2)->where('complete_flg', true)->where('login_user_id', $login_user->id)->orderby('created_at', 'desc')->first();

        // アカウント取得
        $accounts = SearchedAccount::select(['id', 'account_data'])->where('update_time_id', $updated_time->id)->orderby('account_data->following', 'asc')->get();
        Log::debug('未アカウントフォロー:'. print_r($accounts[0]->account_data, true));

        $res_data = [
            'accounts' => $accounts,
            'got_time' => date("Y-m-d H:i:s", strtotime($updated_time->created_at))
        ];
    
        // Log::debug('初期表示データ：' . print_r($res_data, true));
        return $res_data;
    }


    // **
    // 以下 private関数
    // **



    /**
     * 仮想通貨に関連するユーザを取得する。
     * 
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
                'login_user_id' => $login_id // 不要？
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
