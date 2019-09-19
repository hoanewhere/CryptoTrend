<?php

namespace App\Http\Controllers;

use Illuminate\Http\Reest;
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
        $this->getUsers();
        return view('crypto.accountList');
    }

    public function authenticateUser() {

    }
    

    /**
     * 仮想通貨に関連するユーザを取得し、DBに保存する。
     * 
     * @return void
     */
    public function getUsers() {
        Log::debug('getUsers(関数呼び出し)');

        // UpdatedTimeテーブルにレコード追加
        $login_user = Auth::user();
        $login_user_id = $login_user->id;
        $updated_time = New UpdatedTime();
        $updated_time->fill([
            'time_index' => 2,
            'complete_flg' => false,
            'login_user_id' => $login_user_id
        ]);
        $updated_time->save();

        $users = $this->searchUsers();
        $this->saveUsers($users, $login_user_id);
    }


    /**
     * 仮想通貨に関連するユーザを取得する。
     * 
     * @return array $result_arr
     */
    private function searchUsers() {
        Log::debug('serachUsers(関数呼び出し)');

        // TBD: ログインユーザからアクセストークンを取得して関数呼び出す(現状はクライアントユーザで実施)
        $result_arr = TwitterController::searchTweetUsers('TBD', 'TBD');
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
