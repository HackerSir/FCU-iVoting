<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

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
     * @return Response
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
     * @return Response
     */
    public function show($id)
    {
        $setting = Setting::find($id);
        if ($setting) {
            return view('setting.show')->with('setting', $setting);
        }
        return Redirect::route('setting.index')
            ->with('warning', '設定項目不存在');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        $setting = Setting::find($id);
        if ($setting) {
            return view('setting.edit')->with('setting', $setting);
        }
        return Redirect::route('setting.index')
            ->with('warning', '設定項目不存在');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @param Request $request
     * @return Response
     */
    public function update($id, Request $request)
    {
        $setting = Setting::find($id);
        if (!$setting) {
            return Redirect::route('setting.index')
                ->with('warning', '設定項目不存在');
        }

        $validator = Validator::make($request->all(),
            array(
                'data' => 'max:65535'
            )
        );
        if ($validator->fails()) {
            return Redirect::route('setting.edit', $id)
                ->withErrors($validator)
                ->withInput();
        } else {
            $setting->data = $request->get('data');
            $setting->save();
            return Redirect::route('setting.show', $setting->id)
                ->with('global', '設定項目已更新');
        }
    }
}
