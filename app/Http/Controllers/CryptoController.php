<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CryptoController extends Controller
{
    public function index() {
        return view('crypto.trendRanking');
    }

    public function accountList() {
        return view('crypto.accountList');
    }

    public function test_login() {
        return view('crypto.tmp_login');
    }

    public function test_register() {
        return view('crypto.tmp_register');
    }




    public function test() {
        return view('crypto.test');
    }
}
