<?php

namespace App\Http\Controllers;

use Hackersir\Helper\LogHelper;
use Hackersir\Organizer;
use Illuminate\Http\Request;
use Validator;

class OrganizerController extends Controller
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
        $organizerList = Organizer::paginate(20);

        return view('vote.organizer.list')->with('organizerList', $organizerList);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('vote.organizer.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|max:100|unique:organizers',
            'url'      => 'url|max:255',
            'logo_url' => 'url|max:255',
        ]);
        if ($validator->fails()) {
            return redirect()->route('organizer.create')
                ->withErrors($validator)
                ->withInput();
        } else {
            $organizer = Organizer::create([
                'name'     => $request->get('name'),
                'url'      => $request->get('url'),
                'logo_url' => $request->get('logo_url'),
            ]);

            //紀錄
            LogHelper::info(
                '[OrganizerCreated] ' . auth()->user()->email . ' 建立了主辦單位(Id: ' . $organizer->id . ')',
                $organizer
            );

            return redirect()->route('organizer.show', $organizer->id)
                ->with('global', '主辦單位已建立');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $organizer = Organizer::find($id);
        if (!$organizer) {
            return redirect()->route('organizer.index')
                ->with('global', '主辦單位不存在');
        }

        return view('vote.organizer.show')->with('organizer', $organizer);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $organizer = Organizer::find($id);
        if (!$organizer) {
            return redirect()->route('organizer.index')
                ->with('global', '主辦單位不存在');
        }

        return view('vote.organizer.edit')->with('organizer', $organizer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $organizer = Organizer::find($id);
        if (!$organizer) {
            return redirect()->route('organizer.index')
                ->with('global', '主辦單位不存在');
        }
        $validator = Validator::make($request->all(), [
            'name'     => 'required|max:100|unique:organizers,name,' . $id,
            'url'      => 'url|max:255',
            'logo_url' => 'url|max:255',
        ]);
        if ($validator->fails()) {
            return redirect()->route('organizer.edit', $id)
                ->withErrors($validator)
                ->withInput();
        } else {
            //複製一份，在Log時比較差異
            $beforeEdit = $organizer->replicate();

            $organizer->name = $request->get('name');
            $organizer->url = $request->get('url');
            $organizer->logo_url = $request->get('logo_url');
            $organizer->save();

            $afterEdit = $organizer->replicate();

            //紀錄
            LogHelper::info(
                '[OrganizerEdited] ' . Auth::user()->email . ' 編輯了主辦單位(Id: ' . $organizer->id . ')',
                '編輯前',
                $beforeEdit,
                '編輯後',
                $afterEdit
            );

            return redirect()->route('organizer.show', $id)
                ->with('global', '主辦單位已更新');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $organizer = Organizer::find($id);
        if (!$organizer) {
            return redirect()->route('organizer.index')
                ->with('global', '主辦單位不存在');
        }

        //複製一份，在Log時使用
        $beforeDelete = $organizer->replicate();

        $organizer->delete();

        //紀錄
        LogHelper::info(
            '[OrganizerDeleted] ' . auth()->user()->email . ' 刪除了主辦單位(Id: ' . $organizer->id . ')',
            $beforeDelete
        );

        return redirect()->route('organizer.index')
            ->with('global', '主辦單位已移除');
    }
}
