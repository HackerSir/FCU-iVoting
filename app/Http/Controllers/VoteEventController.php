<?php

namespace App\Http\Controllers;

use App\Setting;
use App\VoteEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class VoteEventController extends Controller
{

    public function __construct()
    {
        //限工作人員
        $this->middleware('staff', [
            'except' => [
                'index',
                'show'
            ]
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $voteEventList = VoteEvent::orderBy('id', 'desc')->paginate(20);
        return view('vote.event.list')->with('voteEventList', $voteEventList);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('vote.event.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),
            array(
                'subject' => 'required|max:100',
                'open_time' => 'date',
                'close_time' => 'date',
                'info' => 'max:65535',
                'max_selected' => 'integer|min:1'
            )
        );
        if ($validator->fails()) {
            return Redirect::route('vote-event.create')
                ->withErrors($validator)
                ->withInput();
        } else {
            //檢查時間
            $open_time = ($request->has('open_time')) ? $request->get('open_time') : null;
            $close_time = ($request->has('close_time')) ? $request->get('close_time') : null;
            if ($close_time != null) {
                if ($open_time == null) {
                    $close_time = null;
                } else {
                    if ((new Carbon($open_time))->gte(new Carbon($close_time))) {
                        $close_time = null;
                    }
                }
            }
            $max_selected = ($request->get('max_selected') > 0) ? $request->get('max_selected') : 1;
            $voteEvent = VoteEvent::create(array(
                'subject' => $request->get('subject'),
                'open_time' => $open_time,
                'close_time' => $close_time,
                'info' => $request->get('info'),
                'max_selected' => $max_selected,
            ));
            return Redirect::route('vote-event.show', $voteEvent->id)
                ->with('global', '投票活動已建立');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        $voteEvent = VoteEvent::find($id);
        $autoRedirectSetting = Setting::find('auto-redirect');
        if ($voteEvent) {
            return view('vote.event.show')->with('voteEvent', $voteEvent)->with('autoRedirectSetting', $autoRedirectSetting);
        }
        return Redirect::route('vote-event.index')
            ->with('warning', '投票活動不存在');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        $voteEvent = VoteEvent::find($id);
        if (!$voteEvent) {
            return Redirect::route('vote-event.index')
                ->with('warning', '投票活動不存在');
        }
        if ($voteEvent->isEnded()) {
            return Redirect::route('vote-event.show', $id)
                ->with('warning', '無法編輯已結束之投票活動');
        }
        return view('vote.event.edit')->with('voteEvent', $voteEvent);
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
        $voteEvent = VoteEvent::find($id);
        if (!$voteEvent) {
            return Redirect::route('vote-event.index')
                ->with('warning', '投票活動不存在');
        }
        if ($voteEvent->isEnded()) {
            return Redirect::route('vote-event.show', $id)
                ->with('warning', '無法編輯已結束之投票活動');
        }
        $validator = Validator::make($request->all(),
            array(
                'subject' => 'required|max:100',
                'open_time' => 'date',
                'close_time' => 'date',
                'info' => 'max:65535',
                'max_selected' => 'integer|min:1'
            )
        );
        if ($validator->fails()) {
            return Redirect::route('vote-event.edit', $id)
                ->withErrors($validator)
                ->withInput();
        } else {
            //檢查時間
            if (!$voteEvent->isStarted()) {
                $open_time = ($request->has('open_time')) ? $request->get('open_time') : null;
            } else {
                $open_time = $voteEvent->open_time;
            }
            $close_time = ($request->has('close_time')) ? $request->get('close_time') : null;
            if ($close_time != null) {
                if ($open_time == null) {
                    $close_time = null;
                } else {
                    if ((new Carbon($open_time))->gte(new Carbon($close_time))) {
                        $close_time = null;
                    }
                }
            }
            $voteEvent->subject = $request->get('subject');
            if (!$voteEvent->isStarted()) {
                $voteEvent->open_time = $open_time;
            }
            $voteEvent->close_time = $close_time;
            $voteEvent->info = $request->get('info');
            $voteEvent->max_selected = ($request->get('max_selected') > 0) ? $request->get('max_selected') : 1;
            $voteEvent->save();
            return Redirect::route('vote-event.show', $id)
                ->with('global', '投票活動已更新');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        $voteEvent = VoteEvent::find($id);
        if (!$voteEvent) {
            return Redirect::route('vote-event.index')
                ->with('warning', '投票活動不存在');
        }
        if ($voteEvent->isStarted()) {
            return Redirect::route('vote-event.show', $id)
                ->with('warning', '無法刪除已開始之投票活動');
        }
        //移除投票活動
        $voteEvent->delete();
        return Redirect::route('vote-event.index')
            ->with('global', '投票活動已刪除');
    }

    //開始投票
    public function start($id, Request $request)
    {
        $voteEvent = VoteEvent::find($id);
        if (!$voteEvent) {
            return Redirect::route('vote-event.index')
                ->with('warning', '投票活動不存在');
        }
        if ($voteEvent->isEnded()) {
            return Redirect::route('vote-event.show', $id)
                ->with('warning', '該投票活動早已結束');
        }
        if ($voteEvent->isStarted()) {
            return Redirect::route('vote-event.show', $id)
                ->with('warning', '該投票活動早已開始');
        }
        if ($voteEvent->voteSelections()->count() < 2) {
            return Redirect::route('vote-event.show', $id)
                ->with('warning', '選項過少，無法開始');
        }
        $voteEvent->open_time = Carbon::now()->toDateTimeString();
        $voteEvent->save();
        return Redirect::route('vote-event.show', $id)
            ->with('global', '投票活動已開始');
    }

    //結束投票
    public function end($id, Request $request)
    {
        $voteEvent = VoteEvent::find($id);
        if (!$voteEvent) {
            return Redirect::route('vote-event.index')
                ->with('warning', '投票活動不存在');
        }
        if (!$voteEvent->isStarted()) {
            return Redirect::route('vote-event.show', $id)
                ->with('warning', '該投票活動尚未開始');
        }
        if ($voteEvent->isEnded()) {
            return Redirect::route('vote-event.show', $id)
                ->with('warning', '該投票活動早已結束');
        }
        $voteEvent->close_time = Carbon::now()->toDateTimeString();
        $voteEvent->save();
        return Redirect::route('vote-event.show', $id)
            ->with('global', '投票活動已結束');
    }
}
