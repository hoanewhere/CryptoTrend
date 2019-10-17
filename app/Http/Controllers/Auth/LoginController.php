<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;
    

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = '/top';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        $pattern_login = '/login$/';
        $previous_url = url()->previous();
        
        if (isset($_SERVER['HTTP_REFERER'])) {
            if(!preg_match($pattern_login, $previous_url)) {
                session(['url.intended' => $_SERVER['HTTP_REFERER']]);
            }
        }
        return view('auth.login');
    }

    /**
     * ログインした時のリダイレクト先
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = '/home';
    protected function redirectTo() {
        session()->flash('success', 'ログインしました');
    }


    /**
     * ログアウト後の遷移先を指定
     *
     * @return void
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $this->guard()->logout();

        $request->session()->invalidate();

        $redirect_url = url()->previous();
        return $this->loggedOut($request) ?: redirect($redirect_url)->with('success', 'ログアウトしました');
    }
}
