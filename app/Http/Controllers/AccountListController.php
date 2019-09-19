<?php

namespace App\Http\Controllers;

use Illuminate\Http\Reest;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\TwitterController;

class AccountListController extends Controller
{
    public function index() {
        $this->getUsers();
        return view('crypto.accountList');
    }

    public function authenticateUser() {

    }
    
    public function getUsers() {
        Log::debug('getUsers(関数呼び出し)');
        $users = $this->searchUsers();
        $this->saveUsers($users);
    }

    private function searchUsers() {
        Log::debug('serachUsers(関数呼び出し)');

        // TBD: ログインユーザからアクセストークンを取得して関数呼び出す(現状はクライアントユーザで実施)
        $result_arr = TwitterController::searchTweetUsers('TBD', 'TBD');
        Log::debug('取得データ(result_arr)：'. print_r($result_arr, true));
        Log::debug('取得データの数：'. count($result_arr));
        return $result_arr;
    }

    private function saveUsers(array $users) {
        Log::debug('saveUsers(関数呼び出し)');

        // foreach($users as $user) {
        //     $user_json = json_encode($user);

            
        // }
        return 0;
    }
}
