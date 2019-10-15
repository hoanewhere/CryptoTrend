<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/top');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// 全体
Route::get('/crypto/reloadNavData', 'CryptoController@reloadNavData');

// TOPページ
Route::get('/top', 'TopController@index');

// トレンドランキング
Route::get('/index', 'TrendRankingController@index');
Route::get('/index/reloadTrendData/{term}', 'TrendRankingController@reloadTrendData');

// アカウント一覧
Route::get('/accountList', 'AccountListController@index');
Route::get('/accountList/reloadTweetData', 'AccountListController@reloadTweetData');
Route::get('/accountList/callback', 'AccountListController@callback');
Route::post('/accountList/toFollow', 'AccountListController@toFollow');
Route::post('/accountList/unfollow', 'AccountListController@unfollow');
Route::post('/accountList/toggleAutoFollow', 'AccountListController@toggleAutoFollow');
Route::post('/accountList/connectStart', 'AccountListController@connectStart');
Route::post('/accountList/connectStop', 'AccountListController@connectStop');

// ニュース一覧
Route::get('/newsList', 'NewsListController@index');
Route::get('/newsList/reloadNewsData', 'NewsListController@reloadNews');
