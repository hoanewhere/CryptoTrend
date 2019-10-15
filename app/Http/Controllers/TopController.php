<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TopController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Top Controller
    |--------------------------------------------------------------------------
    |
    | 
    |
    */


    /**
     * TOP画面を表示する。
     * 
     * @return view 'TOP画面'
     */
    public function index() {
        Log::debug('Top index(関数呼び出し)');
        return view('crypto.top');
    }
}
