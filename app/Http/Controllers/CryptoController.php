<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CryptoController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Crypto Controller
    |--------------------------------------------------------------------------
    |
    | 全画面に共通する情報をview側に渡すコントローラ
    |
    */


    /**
     * Navgation bar に表示する情報を取得し、返す。
     * 
     * @return array $nav_list
     */
    public function reloadNavData() {
        // 返り値宣言
        $nav_list = array();
        
        // 検索パターン
        $pattern_login = '/login$/';
        $pattern_register = '/register$/';
        $pattern_password_reset = '/password\/reset/';
        $pattern_index = '/index$/';
        $pattern_account_list = '/accountList$/';
        $pattern_news_list = '/newsList$/';

        // urlとタイトル
        $nav_login = array( "title"=>"ログイン", "url"=>url("login"));
        $nav_register = array( "title"=>"ユーザー登録", "url"=>url("register"));
        $nav_logout = array( "title"=>"ログアウト", "url"=>url("logout"));
        $nav_index = array( "title"=>"トレンド", "url"=>url("index"));
        $nav_account_list = array( "title"=>"アカウント一覧", "url"=>url("accountList"));
        $nav_news_list = array( "title"=>"ニュース一覧", "url"=>url("newsList"));


        // 表示されているurlを取得
        $url = url()->previous();

        //　ログイン状態確認
        if (Auth::check()) {
            if(preg_match($pattern_login, $url)) {
                // 表示されない（index画面に遷移）
            } else if(preg_match($pattern_register, $url)) {
                // 表示されない（index画面に遷移）
            } else if(preg_match($pattern_password_reset, $url)) {
                // 表示されない（index画面に遷移）
            } else if(preg_match($pattern_index, $url)) {
                $nav_list[] = $nav_news_list;
                $nav_list[] = $nav_account_list;
                $nav_list[] = $nav_logout;
            } else if(preg_match($pattern_account_list, $url)) {
                $nav_list[] = $nav_index;
                $nav_list[] = $nav_news_list;
                $nav_list[] = $nav_logout;
            } else if(preg_match($pattern_news_list, $url)) {
                $nav_list[] = $nav_index;
                $nav_list[] = $nav_account_list;
                $nav_list[] = $nav_logout;
            } else {
                // 表示しない
            }
        } else {
            if(preg_match($pattern_login, $url)) {
                $nav_list[] = $nav_register;
                $nav_list[] = $nav_index;
                $nav_list[] = $nav_news_list;
            } else if(preg_match($pattern_register, $url)) {
                $nav_list[] = $nav_login;
                $nav_list[] = $nav_index;
                $nav_list[] = $nav_news_list;
            } else if(preg_match($pattern_password_reset, $url)) {
                $nav_list[] = $nav_register;
                $nav_list[] = $nav_login;
                $nav_list[] = $nav_index;
                $nav_list[] = $nav_news_list;
            } else if(preg_match($pattern_index, $url)) {
                $nav_list[] = $nav_register;
                $nav_list[] = $nav_login;
                $nav_list[] = $nav_news_list;
            } else if(preg_match($pattern_account_list, $url)) {
                // 表示されない（ログイン画面に遷移）
            } else if(preg_match($pattern_news_list, $url)) {
                $nav_list[] = $nav_register;
                $nav_list[] = $nav_login;
                $nav_list[] = $nav_index;
            } else {
                // 表示しない
            }
        }
        return $nav_list;
    }
}
