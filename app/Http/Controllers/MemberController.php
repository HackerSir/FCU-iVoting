<?php namespace App\Http\Controllers;

use App;
use App\Role;
use App\User;
use Carbon\Carbon;
use GrahamCampbell\Throttle\Facades\Throttle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client;
use Exception;

class MemberController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        //遊客限定
        $this->middleware('guest', [
            'only' => [
                'getLogin',
                'postLogin',
                'getRegister',
                'postRegister',
                'getForgotPassword',
                'postForgotPassword',
                'getResetPassword',
                'postResetPassword'
            ]
        ]);
        //會員限定
        $this->middleware('auth', [
            'only' => [
                'getResend',
                'postResend',
                'getChangePassword',
                'postChangePassword',
                'getProfile',
                'getEditProfile',
                'postEditProfile'
            ]
        ]);
        //需完成信箱驗證
        /*$this->middleware('email', [
            'only' => [
                'getIndex',
            ]
        ]);*/
        //限工作人員
        $this->middleware('role:admin', [
            'only' => [
                'getIndex',
                'getEditOtherProfile',
                'postEditOtherProfile'
            ]
        ]);
    }

    //會員清單
    public function getIndex()
    {
        $user = Auth::user();
        //取得會員清單
        $userList = User::paginate(20);
        return view('member.list')->with('userList', $userList);
    }

    //登入
    public function getLogin()
    {
        return view('member.login');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function postLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        //檢查登入冷卻，防止惡意登入
        $throttle = Throttle::get($request, 5, 10);

        //密碼錯誤三次後，追加reCaptcha
        $validator->sometimes('g-recaptcha-response', 'required', function ($input) use ($throttle) {
            return $throttle->count() >= 3;
        });

        if ($validator->fails()) {
            return Redirect::route('member.login')
                ->withErrors($validator)
                ->withInput();
        } else {
            //檢查登入次數
            if (!$throttle->check()) {
                return Redirect::route('member.login')
                    ->with('warning', '嘗試登入過於頻繁，請等待10分鐘。')
                    ->with('delay', 10 * 60)
                    ->withInput();
            }

            //密碼錯誤三次後，追加檢查reCaptcha
            if ($throttle->count() >= 3) {
                $result = $this->tryPassGoogleReCAPTCHA($request);
                if ($result->success !== false) {
                    return Redirect::route('member.login')
                        ->with('warning', '沒有通過 reCAPTCHA 驗證，請再試一次。')
                        ->withInput();
                }
            }

            //增加次數
            $throttle->hit();

            $remember = ($request->has('remember')) ? true : false;
            $auth = Auth::attempt([
                'email' => $request->get('email'),
                'password' => $request->get('password'),
            ], $remember);

            if ($auth) {
                $user = Auth::user();
                //更新資料
                $user->lastlogin_ip = $request->getClientIp();
                $user->lastlogin_at = Carbon::now()->toDateTimeString();
                $user->save();
                //移除重新設定密碼的驗證碼
                DB::table('password_resets')->where('email', '=', $user->email)->delete();
                //記錄
                Log::info('[LoginSucceeded] 登入成功：' . $request->get('email'), [
                    'email' => $request->get('email'),
                    'ip' => $request->getClientIp()
                ]);
                //重導向至登入前頁面
                return Redirect::intended('/');
            } else {
                //紀錄
                Log::info('[LoginFailed] 登入失敗：' . $request->get('email'), [
                    'email' => $request->get('email'),
                    'ip' => $request->getClientIp()
                ]);
                return Redirect::route('member.login')
                    ->with('warning', '帳號或密碼錯誤');
            }
        }
    }

    //註冊
    public function getRegister()
    {
        //註冊允許使用之信箱類型
        $allowedEmails = Config::get('config.allowed_emails');
        $allowedEmailsArray = array(null => '--請下拉選擇--');

        foreach ($allowedEmails as $allowedEmail) {
            $allowedEmailsArray[$allowedEmail] = $allowedEmail;
        }
        if (Config::get('app.debug')) {
            $allowedEmailsDebug = Config::get('config.allowed_emails_debug');
            foreach ($allowedEmailsDebug as $allowedEmail) {
                $allowedEmailsArray[$allowedEmail] = $allowedEmail;
            }
        }

        return view('member.register')->with('allowedEmailsArray', $allowedEmailsArray);
    }

    public function postRegister(Request $request)
    {
        $validator = Validator::make($request->all(),
            array(
                'email_name' => 'required',
                'email_domain' => 'required',
                'password' => 'required|min:6',
                'password_again' => 'required|same:password',
                'g-recaptcha-response' => 'required',
            )
        );

        if ($validator->fails()) {
            return Redirect::route('member.register')
                ->withErrors($validator)
                ->withInput();
        } else {
            $result = $this->tryPassGoogleReCAPTCHA($request);
            if ($result->success !== false) {
                return Redirect::route('member.register')
                    ->with('warning', '沒有通過 reCAPTCHA 驗證，請再試一次。')
                    ->withInput();
            }

            //註冊允許使用之信箱類型
            $allowedEmails = Config::get('config.allowed_emails');
            if (Config::get('app.debug')) {
                $allowedEmailsDebug = Config::get('config.allowed_emails_debug');
                $allowedEmails = array_merge($allowedEmails, $allowedEmailsDebug);
            }
            //取得信箱
            $email_name = $request->get('email_name');
            if (!str_contains($email_name, '@')) {
                //若信箱名稱沒有@，則以下拉選單選擇的域名為準
                $email_domain = $request->get('email_domain');
                $email = $email_name . '@' . $email_domain;
            } else {
                //若信箱名稱含有@，則以信箱名稱作為完整信箱
                $email_domain = preg_split("/@/", $email_name, 2)[1];
                $email = $email_name;
            }
            //檢查Email格式
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return Redirect::route('member.register')
                    ->with('warning', '信箱格式有誤。')
                    ->withInput();
            }
            if (!in_array($email_domain, $allowedEmails)) {
                return Redirect::route('member.register')
                    ->with('warning', '不被允許的信箱類型。')
                    ->withInput();
            }
            if (User::where('email', '=', $email)->count() > 0) {
                return Redirect::route('member.register')
                    ->with('warning', '該信箱已被註冊。')
                    ->withInput();
            }

            $password = $request->get('password');
            //驗證碼
            $code = str_random(60);

            $user = User::create(array(
                'email' => $email,
                'password' => Hash::make($password),
                'confirm_code' => $code,
                'register_ip' => $request->getClientIp(),
                'register_at' => Carbon::now()->toDateTimeString()
            ));

            if ($user) {
                //發送驗證信件
                try {
                    Mail::send('emails.confirm', array('link' => URL::route('member.confirm', $code)), function ($message) use ($user) {
                        $message->to($user->email)->subject("[" . Config::get('config.sitename') . "] 信箱驗證");
                    });
                }
                catch (Exception $e) {
                    Log::info('[RegisterFailed] 註冊失敗：無法寄出認證信給' . $email, [
                        'email' => $email,
                        'ip' => $request->getClientIp()
                    ]);
                    return Redirect::route('member.register')
                        ->with('warning', '無法寄出認證信，請檢查信箱是否填寫錯誤，或是稍後在嘗試。')
                        ->withInput();
                }
                //記錄
                Log::info('[RegisterSucceeded] 註冊成功：' . $email, [
                    'email' => $email,
                    'ip' => $request->getClientIp()
                ]);
                return Redirect::route('home')
                    ->with('global', '註冊完成，請至信箱收取驗證信件並啟用帳號。');
            }
        }
        return Redirect::route('member.register')
            ->with('warning', '註冊時發生錯誤。');
    }

    protected function tryPassGoogleReCAPTCHA(Request $request)
    {
        $client = null;
        if (App::environment('production')) {
            $client = new Client([
                'timeout' => 10.0,
            ]);
        } else {
            $client = new Client([
                'timeout' => 10.0,
                'verify' => false,
            ]);
        }

        $response = $client->post('https://www.google.com/recaptcha/api/siteverify',
            [
                'secret' => env('Secret_Key'),
                'response' => $request->get('g-recaptcha-response'),
                'remoteip' => $request->getClientIp(),
            ]
        );

        return json_decode($response->getBody());
    }

    //驗證信箱
    public function getConfirm($token = null)
    {
        $user = User::where('confirm_code', '=', $token)->whereNull('confirm_at');
        if ($user->count()) {
            $user = $user->first();
            //更新資料
            $user->confirm_at = Carbon::now()->toDateTimeString();
            $user->confirm_code = '';

            if ($user->save()) {
                return Redirect::route('home')
                    ->with('global', '帳號啟用成功。');
            }
        }
        return Redirect::route('home')
            ->with('warning', '驗證連結無效，也可能是帳號已啟用，請再次確認');
    }

    //重發驗證信
    public function getResend()
    {
        $user = Auth::user();
        //帳號已啟用
        if ($user->isConfirmed()) {
            return Redirect::back()
                ->with('warning', '此帳號已啟用，無須再次認證');
        }

        return view('member.resend');
    }

    public function postResend(Request $request)
    {
        $user = Auth::user();
        //帳號已啟用
        if ($user->isConfirmed()) {
            return Redirect::back()
                ->with('warning', '此帳號已啟用，無須再次認證');
        }
        //更換驗證碼
        $code = str_random(60);
        $user->confirm_code = $code;

        if ($user->save()) {
            //重新發送驗證信件
            Mail::send('emails.confirm', array('link' => URL::route('member.confirm', $code)), function ($message) use ($user) {
                $message->to($user->email)->subject("[" . Config::get('config.sitename') . "] 信箱驗證");
            });
            return Redirect::route('home')
                ->with('global', '已重新發送，請至信箱收取驗證信件並啟用帳號。');
        }
        return Redirect::route('member.resend')
            ->with('warning', '發送信件時發生錯誤。');
    }

    //忘記密碼
    public function getForgotPassword()
    {
        return view('member.forgot-password');
    }

    public function postForgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(),
            array(
                'email' => 'required|email',
            )
        );
        if ($validator->fails()) {
            return Redirect::route('member.forgot-password')
                ->withErrors($validator)
                ->withInput();
        } else {
            $user = User::where('email', '=', $request->get('email'));
            if ($user->count()) {
                $user = $user->first();
                $code = str_random(60);
                //檢查是否曾有驗證碼記錄
                if (DB::table('password_resets')->where('email', '=', $user->email)->count()) {
                    //更新找回密碼的驗證碼
                    DB::table('password_resets')->where('email', '=', $user->email)->update([
                        'token' => $code,
                        'updated_at' => Carbon::now()->toDateTimeString()
                    ]);
                } else {
                    //產生找回密碼的驗證碼
                    DB::table('password_resets')->insert([
                        'email' => $user->email,
                        'token' => $code,
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString()
                    ]);
                }
                if ($user->save()) {
                    //發送信件
                    Mail::send('emails.forgot', array('link' => URL::route('member.reset-password', $code)), function ($message) use ($user) {
                        $message->to($user->email)->subject("[" . Config::get('config.sitename') . "] 重新設定密碼");
                    });
                    return Redirect::route('home')
                        ->with('global', '更換密碼的連結已發送至信箱。');
                }
            } else {
                return Redirect::route('member.forgot-password')
                    ->with('warning', '此信箱仍未註冊成為會員。');
            }
        }
        return Redirect::route('member.forgot-password')
            ->with('warning', '無法取得更換密碼的連結。');
    }

    //重設密碼
    public function getResetPassword($token = null)
    {
        if (DB::table('password_resets')->where('token', '=', $token)->count()) {
            $email = DB::table('password_resets')->where('token', '=', $token)->first()->email;
            $user = User::where('email', '=', $email)->first();
            //檢查使用者是否存在
            if ($user) {
                return view('member.reset-password')->with('user', $user)->with('token', $token);
            }
        }
        return Redirect::route('home')
            ->with('warning', '連結無效，無法重新設定密碼，請再次確認');
    }

    public function postResetPassword(Request $request)
    {
        $token = $request->get('token');
        if (DB::table('password_resets')->where('token', '=', $token)->count()) {
            $email = DB::table('password_resets')->where('token', '=', $token)->first()->email;
            $user = User::where('email', '=', $email)->first();
            //檢查使用者是否存在
            if ($user) {
                $validator = Validator::make($request->all(),
                    array(
                        'password' => 'required|min:6',
                        'password_again' => 'required|same:password',
                    )
                );

                if ($validator->fails()) {
                    return Redirect::route('member.reset-password', $token)
                        ->withErrors($validator)
                        ->withInput();
                } else {
                    $password = $request->get('password');
                    $user->password = Hash::make($password);

                    if ($user->save()) {
                        //移除重新設定密碼的驗證碼
                        DB::table('password_resets')->where('email', '=', $email)->delete();
                        return Redirect::route('home')
                            ->with('global', '密碼重新設定完成，請使用新密碼重新登入。');
                    }
                }
            }
        }
        return Redirect::route('member.change-password')
            ->with('warning', '密碼無法修改。');
    }

    //修改密碼
    public function getChangePassword()
    {
        return view('member.change-password');
    }

    public function postChangePassword(Request $request)
    {
        $validator = Validator::make($request->all(),
            array(
                'old_password' => 'required',
                'password' => 'required|min:6',
                'password_again' => 'required|same:password',
            )
        );

        if ($validator->fails()) {
            return Redirect::route('member.change-password')
                ->withErrors($validator)
                ->withInput();
        } else {
            $user = Auth::user();

            $old_password = $request->get('old_password');
            $password = $request->get('password');

            if (Hash::check($old_password, $user->getAuthPassword())) {
                $user->password = Hash::make($password);

                if ($user->save()) {
                    return Redirect::route('home')
                        ->with('global', '密碼修改完成。');
                }
            } else {
                return Redirect::route('member.change-password')
                    ->with('warning', '舊密碼輸入錯誤。');
            }
        }
        return Redirect::route('member.change-password')
            ->with('warning', '密碼無法修改。');
    }

    //個人資料
    public function getProfile($uid = null)
    {
        $user = Auth::user();
        if (empty($uid)) {
            return view('member.profile')->with('user', $user);
        } else {
            //只有工作人員或資料主人可以查看
            if ($user->isStaff() || $user->id == $uid) {
                $showUser = User::find($uid);
                if ($showUser) {
                    return view('member.other-profile')->with('user', $user)->with('showUser', $showUser);
                } else {
                    return Redirect::route('home')
                        ->with('warning', '該成員不存在。');
                }
            } else {
                return Redirect::route('home')
                    ->with('warning', '無權查看他人資料。');
            }
        }
    }
    /*
        //修改資料
        public function getEditProfile()
        {
            $user = Auth::user();
            return view('member.edit-profile')->with('user', $user);
        }

        public function postEditProfile(Request $request)
        {
            $validator = Validator::make($request->all(),
                array(
                    'nid' => array(
                        'size:8',
                        'unique:users,nid,' . Auth::user()->id,
                        'regex:/^([depm]([0-9]){7})|(t[0-9]{5})$/i'
                    ),
                )
            );

            if ($validator->fails()) {
                return Redirect::route('member.edit-profile')
                    ->withErrors($validator)
                    ->withInput();
            } else {
                $user = Auth::user();
                if (empty($user->nid)) {
                    $user->nid = strtoupper($request->get('nid'));
                }
                if ($user->save()) {
                    return Redirect::route('member.profile')
                        ->with('global', '個人資料修改完成。');
                }
            }
            return Redirect::route('member.edit-profile')
                ->with('warning', '個人資料無法修改。');
        }
    */
    //修改他人資料
    public function getEditOtherProfile($uid)
    {
        $showUser = User::find($uid);
        if ($showUser) {
            $roleList = Role::all();
            return view('member.edit-other-profile')->with('showUser', $showUser)->with('roleList', $roleList);
        } else {
            return Redirect::route('member.list')
                ->with('warning', '該成員不存在。');
        }
    }

    public function postEditOtherProfile(Request $request, $uid = null)
    {
        $user = Auth::user();
        $showUser = User::find($uid);
        if (!$showUser) {
            return Redirect::route('member.list')
                ->with('warning', '該成員不存在。');
        }

        $validator = Validator::make($request->all(),
            array(
                'nid' => array(
                    'size:8',
                    'unique:users,nid,' . $showUser->id,
                    'regex:/^([depm]([0-9]){7})|(t[0-9]{5})$/i'
                ),
            )
        );

        if ($validator->fails()) {
            return Redirect::route('member.edit-other-profile', $uid)
                ->withErrors($validator)
                ->withInput();
        } else {
            $showUser->nid = strtoupper($request->get('nid'));
            //管理員禁止去除自己的管理員職務
            $keepAdmin = false;
            if ($showUser->id == Auth::user()->id) {
                $keepAdmin = true;
            }
            //移除原有權限
            $showUser->detachRoles($showUser->roles);
            //重新添加該有的權限
            if ($request->has('role')) {
                $showUser->attachRoles($request->get('role'));
            }
            if ($keepAdmin) {
                $admin = Role::where('name', '=', 'admin')->first();
                $showUser->attachRole($admin);
            }
            //儲存資料
            if ($showUser->save()) {
                return Redirect::route('member.profile', $uid)
                    ->with('global', '資料修改完成。');
            }
        }
        return Redirect::route('member.edit-other-profile', $uid)
            ->with('warning', '資料無法修改。');
    }

    //登出
    public function getLogout()
    {
        Auth::logout();
        return Redirect::route('home');
    }

}
