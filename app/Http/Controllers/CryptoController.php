<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CryptoController extends Controller
{
    public function index() {
        return view('crypto.trendRanking');
    }

    public function test_login() {
        return view('crypto.tmp_login');
    }

    public function test_register() {
        return view('crypto.tmp_register');
    }
}
