<?php

namespace App\Http\Controllers;

use Hackersir\User;
use Carbon\Carbon;
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

            $data['會員人數'] = $this->formatNumberDataInfo($userCount);

            $data['已驗證會員人數'] = $this->formatNumberDataInfo(User::whereNotNull('confirm_at')->count(), $userCount);
            $data['未驗證會員人數'] = $this->formatNumberDataInfo(User::whereNull('confirm_at')->count(), $userCount);

            $data['100年入學會員數（d00xxxxx@fcu.edu.tw）'] = $this->formatNumberDataInfo(
                User::whereNotNull('confirm_at')->where('email', 'like', 'd00%fcu.edu.tw')->count(),
                $userCount
            );

            $data['101年入學會員數（d01xxxxx@fcu.edu.tw）'] = $this->formatNumberDataInfo(
                User::whereNotNull('confirm_at')->where('email', 'like', 'd01%fcu.edu.tw')->count(),
                $userCount
            );
            $data['102年入學會員數（d02xxxxx@fcu.edu.tw）'] = $this->formatNumberDataInfo(
                User::whereNotNull('confirm_at')->where('email', 'like', 'd02%fcu.edu.tw')->count(),
                $userCount
            );
            $data['103年入學會員數（d03xxxxx@fcu.edu.tw）'] = $this->formatNumberDataInfo(
                User::whereNotNull('confirm_at')->where('email', 'like', 'd03%fcu.edu.tw')->count(),
                $userCount
            );
            $data['104年入學會員數（d04xxxxx@fcu.edu.tw）'] = $this->formatNumberDataInfo(
                User::whereNotNull('confirm_at')->where('email', 'like', 'd04%fcu.edu.tw')->count(),
                $userCount
            );

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

    //數值類資料顯示
    private function formatNumberDataInfo($number, $denominator = 0)
    {
        //防止非數字
        if (!is_numeric($number)) {
            return $number;
        }
        if ($denominator > 0) {
            //需額外以比例顯示
            //數字的括號用半形就好
            return sprintf('%6s (%6.2f %%)', number_format($number), round($number / $denominator * 100, 2));
        } else {
            //僅顯示數值
            return sprintf('%6s', number_format($number));
        }
    }
}
