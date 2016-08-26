<?php

namespace App\Http\Controllers;

use Hackersir\Helper\JsonHelper;
use Hackersir\Helper\LogHelper;
use Hackersir\Organizer;
use Hackersir\Setting;
use Hackersir\VoteEvent;
use Hackersir\VoteSelection;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->check() && auth()->user()->isStaff()) {
            $voteEventList = VoteEvent::orderBy('id', 'desc')->paginate(20);
        } else {
            $voteEventList = VoteEvent::orderBy('id', 'desc')->where(function ($query) {
                $query->where('show', true)
                    ->orWhere('open_time', '<=', Carbon::now());
            })->paginate(20);
        }

        return view('vote.event.list', compact('voteEventList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $organizerList = Organizer::all();
        $organizerArray = [null => '-- 請下拉選擇主辦單位 --'];
        foreach ($organizerList as $organizer) {
            $organizerArray[$organizer->id] = $organizer->name;
        }

        return view('vote.event.create', compact('organizerArray'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'subject'      => 'required|max:100',
            'open_time'    => 'date',
            'close_time'   => 'date',
            'info'         => 'max:65535',
            'max_selected' => 'integer|min:1',
            'award_count'  => 'integer|min:1',
        ]);
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
            'show_result'    => $request->get('show_result'),
            'award_count'    => ($request->get('award_count') > 0) ? $request->get('award_count') : 1,
        ]);

        //紀錄
        LogHelper::info(
            '[VoteEventCreated] ' . auth()->user()->email . ' 建立了活動(Id: ' . $voteEvent->id
            . ', Subject: ' . $voteEvent->subject . ')',
            $voteEvent
        );

        return redirect()->route('voteEvent.show', $voteEvent->id)
            ->with('global', '投票活動已建立');
    }

    /**
     * Display the specified resource.
     *
     * @param VoteEvent $voteEvent
     * @return \Illuminate\Http\Response
     */
    public function show(VoteEvent $voteEvent)
    {
        $autoRedirectSetting = Setting::find('auto-redirect');
        if ((auth()->check() && auth()->user()->isStaff()) || $voteEvent->isVisible()) {
            return view('vote.event.show-eas', compact(['voteEvent', 'autoRedirectSetting']));
        } else {
            return redirect()->route('voteEvent.index')
                ->with('warning', '投票活動尚未開放');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param VoteEvent $voteEvent
     * @return \Illuminate\Http\Response
     */
    public function edit(VoteEvent $voteEvent)
    {
        if ($voteEvent->isEnded()) {
            return redirect()->route('voteEvent.show', $voteEvent)
                ->with('warning', '無法編輯已結束之投票活動');
        }
        $organizerList = Organizer::all();
        $organizerArray = [null => '-- 請下拉選擇主辦單位 --'];
        foreach ($organizerList as $organizer) {
            $organizerArray[$organizer->id] = $organizer->name;
        }

        return view('vote.event.edit', compact(['voteEvent', 'organizerArray']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param VoteEvent $voteEvent
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, VoteEvent $voteEvent)
    {
        if ($voteEvent->isEnded()) {
            return redirect()->route('voteEvent.show', $voteEvent)
                ->with('warning', '無法編輯已結束之投票活動');
        }
        $this->validate($request, [
            'subject'      => 'required|max:100',
            'open_time'    => 'date',
            'close_time'   => 'date',
            'info'         => 'max:65535',
            'max_selected' => 'integer|min:1',
            'award_count'  => 'integer|min:1',
        ]);
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
            '[VoteEventEdited] ' . auth()->user()->email . ' 編輯了活動(Id: ' . $voteEvent->id
            . ', Subject: ' . $voteEvent->subject . ')',
            '編輯前',
            $beforeEdit,
            '編輯後',
            $afterEdit
        );

        return redirect()->route('voteEvent.show', $voteEvent)
            ->with('global', '投票活動已更新');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param VoteEvent $voteEvent
     * @return \Illuminate\Http\Response
     */
    public function destroy(VoteEvent $voteEvent)
    {
        if ($voteEvent->isStarted()) {
            return redirect()->route('voteEvent.show', $voteEvent)
                ->with('warning', '無法刪除已開始之投票活動');
        }
        //移除投票活動
        $voteEvent->delete();

        return redirect()->route('voteEvent.index')
            ->with('global', '投票活動已刪除');
    }

    //開始投票
    public function start(Request $request, VoteEvent $voteEvent)
    {
        if ($voteEvent->isEnded()) {
            return redirect()->route('voteEvent.show', $voteEvent)
                ->with('warning', '該投票活動早已結束');
        }
        if ($voteEvent->isStarted()) {
            return redirect()->route('voteEvent.show', $voteEvent)
                ->with('warning', '該投票活動早已開始');
        }
        if ($voteEvent->voteSelections()->count() < 2) {
            return redirect()->route('voteEvent.show', $voteEvent)
                ->with('warning', '選項過少，無法開始');
        }
        $voteEvent->update([
            'open_time' => Carbon::now()->toDateTimeString(),
        ]);

        return redirect()->route('voteEvent.show', $voteEvent)
            ->with('global', '投票活動已開始');
    }

    //結束投票
    public function end(Request $request, VoteEvent $voteEvent)
    {
        if (!$voteEvent->isStarted()) {
            return redirect()->route('voteEvent.show', $voteEvent)
                ->with('warning', '該投票活動尚未開始');
        }
        if ($voteEvent->isEnded()) {
            return redirect()->route('voteEvent.show', $voteEvent)
                ->with('warning', '該投票活動早已結束');
        }
        $voteEvent->update([
            'close_time' => Carbon::now()->toDateTimeString(),
        ]);

        return redirect()->route('voteEvent.show', $voteEvent)
            ->with('global', '投票活動已結束');
    }

    //排序選項
    public function sort(Request $request, VoteEvent $voteEvent)
    {
        //只接受Ajax請求
        if (!$request->ajax()) {
            return 'error';
        }
        //若無選項，直接回傳成功
        if ($voteEvent->voteSelections->count() == 0) {
            return 'success';
        }
        //取得原選項順序
        $originalIdList = $voteEvent->voteSelections->pluck('title', 'id')->toArray();
        //取得排序後的id清單
        $idList = $request->get('idList');
        foreach ($idList as $order => $id) {
            $selection = VoteSelection::find($id);
            $selection->update([
                'order' => $order,
            ]);
        }
        //更新$voteEvent資料
        $voteEvent = VoteEvent::find($voteEvent->id);
        //取得新選項順序
        $newIdList = $voteEvent->voteSelections->pluck('title', 'id')->toArray();
        //若不同則紀錄
        if ($originalIdList !== $newIdList) {
            //Log
            LogHelper::info(
                '[VoteSelectionOrderEdited] ' . auth()->user()->email . ' 編輯了選項排序(Id: ' . $voteEvent->id
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
