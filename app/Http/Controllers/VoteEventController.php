<?php

namespace App\Http\Controllers;

use App\Helper\JsonHelper;
use App\Helper\LogHelper;
use App\Organizer;
use App\Setting;
use App\VoteEvent;
use App\VoteSelection;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class VoteEventController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        //限工作人員
        $this->middleware('role:staff', [
            'except' => [
                'index',
                'show',
            ],
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        if (Auth::check() && Auth::user()->isStaff()) {
            $voteEventList = VoteEvent::orderBy('id', 'desc')->paginate(20);
        } else {
            $voteEventList = VoteEvent::orderBy('id', 'desc')->where(function ($query) {
                $query->where('show', true)
                    ->orWhere('open_time', '<=', Carbon::now());
            })->paginate(20);
        }

        return view('vote.event.list')->with('voteEventList', $voteEventList);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $organizerList = Organizer::all();
        $organizerArray = [null => '-- 請下拉選擇主辦單位 --'];
        foreach ($organizerList as $organizer) {
            $organizerArray[$organizer->id] = $organizer->name;
        }

        return view('vote.event.create')->with('organizerArray', $organizerArray);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject'      => 'required|max:100',
            'open_time'    => 'date',
            'close_time'   => 'date',
            'info'         => 'max:65535',
            'max_selected' => 'integer|min:1',
            'award_count'  => 'integer|min:1',
        ]);
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

            //投票條件
            $condition = new \stdClass();
            $condition->prefix = ($request->has('prefix')) ? str_replace(' ', '', $request->get('prefix')) : null;

            $voteEvent = VoteEvent::create([
                'subject'        => $request->get('subject'),
                'open_time'      => $open_time,
                'close_time'     => $close_time,
                'info'           => $request->get('info'),
                'max_selected'   => ($request->get('max_selected') > 0) ? $request->get('max_selected') : 1,
                'organizer_id'   => ($request->has('organizer')) ? $request->get('organizer') : null,
                'show'           => !$request->get('hideVoteEvent', false),
                'vote_condition' => (!empty($condition))
                    ? JsonHelper::encode((object) array_filter((array) $condition))
                    : null,
                'show_result' => $request->get('show_result'),
                'award_count' => ($request->get('award_count') > 0) ? $request->get('award_count') : 1,
            ]);

            //紀錄
            LogHelper::info(
                '[VoteEventCreated] ' . Auth::user()->email . ' 建立了活動(Id: ' . $voteEvent->id
                . ', Subject: ' . $voteEvent->subject . ')',
                $voteEvent
            );

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
            if ((Auth::check() && Auth::user()->isStaff()) || $voteEvent->isVisible()) {
                return view('vote.event.show-eas')
                    ->with('voteEvent', $voteEvent)
                    ->with('autoRedirectSetting', $autoRedirectSetting);
            } else {
                return Redirect::route('vote-event.index')
                    ->with('warning', '投票活動尚未開放');
            }
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
        $organizerList = Organizer::all();
        $organizerArray = [null => '-- 請下拉選擇主辦單位 --'];
        foreach ($organizerList as $organizer) {
            $organizerArray[$organizer->id] = $organizer->name;
        }

        return view('vote.event.edit')->with('voteEvent', $voteEvent)->with('organizerArray', $organizerArray);
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
        $validator = Validator::make($request->all(), [
            'subject'      => 'required|max:100',
            'open_time'    => 'date',
            'close_time'   => 'date',
            'info'         => 'max:65535',
            'max_selected' => 'integer|min:1',
            'award_count'  => 'integer|min:1',
        ]);
        if ($validator->fails()) {
            return Redirect::route('vote-event.edit', $id)
                ->withErrors($validator)
                ->withInput();
        } else {
            //複製一份，在Log時比較差異
            $beforeEdit = $voteEvent->replicate();

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
                $voteEvent->max_selected = ($request->get('max_selected') > 0) ? $request->get('max_selected') : 1;
                $voteEvent->organizer_id = ($request->has('organizer')) ? $request->get('organizer') : null;
                $voteEvent->show = !$request->get('hideVoteEvent', false);
                $voteEvent->award_count = ($request->get('award_count') > 0) ? $request->get('award_count') : 1;
            }
            $voteEvent->close_time = $close_time;
            $voteEvent->info = $request->get('info');
            //投票條件
            $condition = new \stdClass();
            $condition->prefix = ($request->has('prefix')) ? str_replace(' ', '', $request->get('prefix')) : null;
            $voteEvent->vote_condition = (!empty($condition))
                ? JsonHelper::encode((object) array_filter((array) $condition))
                : null;

            $voteEvent->show_result = $request->get('show_result');

            $voteEvent->save();

            $afterEdit = $voteEvent->replicate();

            //Log
            LogHelper::info(
                '[VoteEventEdited] ' . Auth::user()->email . ' 編輯了活動(Id: ' . $voteEvent->id
                . ', Subject: ' . $voteEvent->subject . ')',
                '編輯前',
                $beforeEdit,
                '編輯後',
                $afterEdit
            );

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

    //排序選項
    public function sort($id, Request $request)
    {
        //只接受Ajax請求
        if (!$request->ajax()) {
            return 'error';
        }
        $voteEvent = VoteEvent::find($id);
        if (!$voteEvent) {
            return '投票活動不存在';
        }
        //若無選項，直接回傳成功
        if ($voteEvent->voteSelections->count() == 0) {
            return 'success';
        }
        //取得原選項順序
        $originalIdList = $voteEvent->voteSelections->lists('title', 'id')->toArray();
        //取得排序後的id清單
        $idList = $request->get('idList');
        foreach ($idList as $order => $id) {
            $selection = VoteSelection::find($id);
            $selection->order = $order;
            $selection->save();
        }
        //更新$voteEvent資料
        $voteEvent = VoteEvent::find($voteEvent->id);
        //取得新選項順序
        $newIdList = $voteEvent->voteSelections->lists('title', 'id')->toArray();
        //若不同則紀錄
        if ($originalIdList !== $newIdList) {
            //Log
            LogHelper::info(
                '[VoteSelectionOrderEdited] ' . Auth::user()->email . ' 編輯了選項排序(Id: ' . $voteEvent->id
                . ', Subject: ' . $voteEvent->subject . ')',
                '編輯前',
                $originalIdList,
                '編輯後',
                $newIdList
            );
        }

        return 'success';
    }
}
