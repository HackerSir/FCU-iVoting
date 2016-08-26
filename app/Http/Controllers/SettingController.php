<?php

namespace App\Http\Controllers;

use Hackersir\Helper\LogHelper;
use Hackersir\Setting;
use Exception;
use Illuminate\Http\Request;
use Log;
use Mail;

class SettingController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        //限管理員
        $this->middleware('role:admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $settingList = Setting::all();

        return view('setting.list', compact('settingList'));
    }

    /**
     * Display the specified resource.
     *
     * @param Setting $setting
     * @return \Illuminate\Http\Response
     */
    public function show(Setting $setting)
    {
        return view('setting.show', compact('setting'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Setting $setting
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function edit(Setting $setting)
    {
        return view('setting.edit', compact('setting'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Setting $setting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Setting $setting)
    {
        $this->validate($request, [
            'data' => 'max:65535',
        ]);
        $setting->update($request->only(['data']));

        return redirect()->route('setting.show', $setting)
            ->with('global', '設定項目已更新');
    }

    public function sendTestMail(Request $request)
    {
        //只接受Ajax請求
        if (!$request->ajax()) {
            return 'error';
        }

        $email = $request->get('email');
        $type = $request->get('type');

        if ($type == 'normal') {
            try {
                Mail::raw('這是測試信。', function ($message) use ($email) {
                    $message->to($email)->subject('[' . config('config.sitename') . '] 測試信');
                });
            } catch (Exception $e) {
                //Log
                LogHelper::info('[MailSendFailed] 無法發送測試信' . $email);
                Log::error($e);

                return 'error';
            }
        } elseif ($type == 'queue') {
            Mail::queue(
                'emails.raw',
                ['text' => '這是測試信。'],
                function ($message) use ($email) {
                    $message->to($email)->subject('[' . config('config.sitename') . '] 測試信');
                }
            );
        } else {
            return 'error';
        }

        return 'success';
    }
}
