<?php

namespace App\Http\Controllers;

use Hackersir\Helper\LogHelper;
use Hackersir\Setting;
use Exception;
use Illuminate\Http\Request;
use Mail;
use Validator;

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

        return view('setting.list')->with('settingList', $settingList);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $setting = Setting::find($id);
        if ($setting) {
            return view('setting.show')->with('setting', $setting);
        }

        return redirect()->route('setting.index')
            ->with('warning', '設定項目不存在');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $setting = Setting::find($id);
        if ($setting) {
            return view('setting.edit')->with('setting', $setting);
        }

        return redirect()->route('setting.index')
            ->with('warning', '設定項目不存在');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        $setting = Setting::find($id);
        if (!$setting) {
            return redirect()->route('setting.index')
                ->with('warning', '設定項目不存在');
        }

        $validator = Validator::make($request->all(), [
            'data' => 'max:65535',
        ]);
        if ($validator->fails()) {
            return redirect()->route('setting.edit', $id)
                ->withErrors($validator)
                ->withInput();
        } else {
            $setting->data = $request->get('data');
            $setting->save();

            return redirect()->route('setting.show', $setting->id)
                ->with('global', '設定項目已更新');
        }
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
