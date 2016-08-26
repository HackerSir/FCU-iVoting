<?php

namespace App\Http\Controllers;

use Hackersir\Helper\LogHelper;
use Hackersir\Organizer;
use Illuminate\Http\Request;

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
        $organizerList = Organizer::paginate();

        return view('vote.organizer.list', compact('organizerList'));
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
        $this->validate($request, [
            'name'     => 'required|max:100|unique:organizers',
            'url'      => 'url|max:255',
            'logo_url' => 'url|max:255',
        ]);
        $organizer = Organizer::create($request->all());

        //紀錄
        LogHelper::info(
            '[OrganizerCreated] ' . auth()->user()->email . ' 建立了主辦單位(Id: ' . $organizer->id . ')',
            $organizer
        );

        return redirect()->route('organizer.show', $organizer->id)
            ->with('global', '主辦單位已建立');
    }

    /**
     * Display the specified resource.
     *
     * @param Organizer $organizer
     * @return \Illuminate\Http\Response
     */
    public function show(Organizer $organizer)
    {
        return view('vote.organizer.show', compact('organizer'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Organizer $organizer
     * @return \Illuminate\Http\Response
     */
    public function edit(Organizer $organizer)
    {
        return view('vote.organizer.edit', compact('organizer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param Organizer $organizer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Organizer $organizer)
    {
        $this->validate($request, [
            'name'     => 'required|max:100|unique:organizers,name,' . $organizer->id,
            'url'      => 'url|max:255',
            'logo_url' => 'url|max:255',
        ]);
        //複製一份，在Log時比較差異
        $beforeEdit = $organizer->replicate();

        $organizer->update($request->all());

        $afterEdit = $organizer->replicate();

        //紀錄
        LogHelper::info(
            '[OrganizerEdited] ' . auth()->user()->email . ' 編輯了主辦單位(Id: ' . $organizer->id . ')',
            '編輯前',
            $beforeEdit,
            '編輯後',
            $afterEdit
        );

        return redirect()->route('organizer.show', $organizer)
            ->with('global', '主辦單位已更新');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Organizer $organizer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Organizer $organizer)
    {
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
