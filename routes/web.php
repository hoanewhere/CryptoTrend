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
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
// Route::get('/crypto/login', 'CryptoController@test_login');
Route::get('/crypto/register', 'CryptoController@test_register');
Route::get('/index', 'TrendRankingController@index');
Route::get('/accountList', 'AccountListController@index');
Route::get('/accountList/callback', 'AccountListController@callback');


// test
Route::get('/test', 'CryptoController@test');

