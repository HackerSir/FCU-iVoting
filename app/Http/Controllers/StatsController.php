<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;

class StatsController extends Controller
{
    protected static $cacheMinute = 60;      //統計資訊緩存時間（分鐘）

    public function __construct()
    {
        parent::__construct();
        //限管理員
        $this->middleware('role:admin');
    }

    //主頁面
    public function getIndex()
    {
        //設定緩存變數
        $stats = Cache::remember('stats', self::$cacheMinute, function () {
            //計算各種資訊
            $newStats = new \stdClass();
            //最後更新時間
            $newStats->time = Carbon::now();
            //統計資料
            $data = [];
            $userCount = User::count();
            $data['會員人數'] = $userCount;
            $confirmedUserCount = User::whereNotNull('confirm_at')->count();
            $data['已驗證會員人數'] = $confirmedUserCount . '（' . round($confirmedUserCount / $userCount * 100, 2) . '%）';
            $unconfirmedUserCount = User::whereNull('confirm_at')->count();
            $data['未驗證會員人數'] = $unconfirmedUserCount . '（' . round($unconfirmedUserCount / $userCount * 100, 2) . '%）';
            $newStats->data = $data;
            return $newStats;
        });
        return view('stats.index')->with('stats', $stats)->with('cacheMinute', self::$cacheMinute);
    }

    //強制更新
    public function getForceRenew()
    {
        //清除緩存
        Cache::forget('stats');
        //重新導向
        return Redirect::route('stats.index')->with('global', '統計資料已更新');
    }
}
