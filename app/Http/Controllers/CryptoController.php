<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CryptoController extends Controller
{
    public function test_login() {
        return view('crypto.tmp_login');
    }

    public function test_register() {
        return view('crypto.tmp_register');
    }
}
