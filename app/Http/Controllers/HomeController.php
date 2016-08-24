<?php

namespace App\Http\Controllers;

use Hackersir\Setting;
use Illuminate\Support\Facades\Redirect;

class HomeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Home Controller
    |--------------------------------------------------------------------------
    |
    | This controller renders your application's "dashboard" for users that
    | are authenticated. Of course, you are free to change or remove the
    | controller as you wish. It is just here to get your app started!
    |
    */

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct();
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard to the user.
     * @return Response
     */
    public function index()
    {
        //檢查是否設定自動跳轉網址
        $autoRedirect = Setting::get('auto-redirect');
        if (filter_var($autoRedirect, FILTER_VALIDATE_URL)) {
            return Redirect::to($autoRedirect);
        }

        return $this->home();
    }

    public function home()
    {
        return view('home');
    }
}
