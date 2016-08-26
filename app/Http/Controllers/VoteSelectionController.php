<?php

namespace App\Http\Controllers;

use Hackersir\Helper\JsonHelper;
use Hackersir\Helper\LogHelper;
use Hackersir\VoteBallot;
use Hackersir\VoteEvent;
use Hackersir\VoteSelection;
use Illuminate\Http\Request;
use Input;
use stdClass;
use Validator;

class VoteSelectionController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        //Email必須驗證
        $this->middleware('email', [
            'only' => [
                'vote',
            ],
        ]);
        //限工作人員
        $this->middleware('role:staff', [
            'except' => [
                'index',
                'show',
                'vote',
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $vid = Input::get('vid');
        if (empty($vid) || !is_numeric($vid)) {
            return redirect()->route('voteEvent.index')
                ->with('warning', '請選擇投票活動');
        }
        $voteEvent = VoteEvent::find($vid);
        if ($voteEvent == null) {
            return redirect()->route('voteEvent.index')
                ->with('warning', '投票活動不存在');
        }
        if ($voteEvent->isStarted()) {
            return redirect()->route('voteEvent.show', $voteEvent)
                ->with('warning', '只能在投票活動開始前編輯選項');
        }

        return view('vote.selection.create', compact('voteEvent'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $vid = $request->get('vid');
        if (empty($vid) || !is_numeric($vid)) {
            return redirect()->route('voteEvent.index')
                ->with('warning', '請選擇投票活動');
        }
        $voteEvent = VoteEvent::find($vid);
        if ($voteEvent == null) {
            return redirect()->route('voteEvent.index')
                ->with('warning', '投票活動不存在');
        }
        if ($voteEvent->isStarted()) {
            return redirect()->route('voteEvent.show', $voteEvent)
                ->with('warning', '只能在投票活動開始前編輯選項');
        }
        $this->validate($request, [
            'title'  => 'required|max:65535',
            'weight' => 'numeric',
            'image'  => 'max:65535',
        ]);
        //封裝JSON
        $obj = new stdClass();
        //$obj->image = explode(PHP_EOL, $request->get('image'));
        $obj->image = preg_split('/(\n|\r|\n\r)/', $request->get('image'), null, PREG_SPLIT_NO_EMPTY);
        $json = JsonHelper::encode($obj);
        $order = ($voteEvent->voteSelections->count() > 0) ? $voteEvent->voteSelections->max('order') + 1 : 0;
        $voteSelection = VoteSelection::create([
            'title'         => $request->get('title'),
            'vote_event_id' => $voteEvent->id,
            'weight'        => $request->has('weight') ? $request->get('weight') : 1,
            'data'          => $json,
            'order'         => $order,
        ]);

        //紀錄
        LogHelper::info(
            '[VoteSelectionCreated] ' .
            auth()->user()->email . ' 為 ' . $voteEvent->subject . ' 建立選項',
            $voteSelection
        );

        return redirect()->route('voteEvent.show', $voteSelection->voteEvent)
            ->with('global', '投票選項已建立');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param VoteSelection $voteSelection
     * @return \Illuminate\Http\Response
     */
    public function edit(VoteSelection $voteSelection)
    {
        if ($voteSelection->voteEvent->isStarted()) {
            return redirect()->route('voteEvent.show', $voteSelection->voteEvent)
                ->with('warning', '只能在投票活動開始前編輯選項');
        }

        return view('vote.selection.edit', compact('voteSelection'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param VoteSelection $voteSelection
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, VoteSelection $voteSelection)
    {
        if ($voteSelection->voteEvent->isStarted()) {
            return redirect()->route('voteEvent.show', $voteSelection->voteEvent)
                ->with('warning', '只能在投票活動開始前編輯選項');
        }
        $this->validate($request, [
            'title'  => 'required|max:65535',
            'weight' => 'numeric',
            'image'  => 'max:65535',
        ]);
        //複製一份，在Log時比較差異
        $beforeEdit = $voteSelection->replicate();
        //封裝JSON
        $obj = new stdClass();
        //$obj->image = explode(PHP_EOL, $request->get('image'));
        $obj->image = preg_split('/(\n|\r|\n\r)/', $request->get('image'), null, PREG_SPLIT_NO_EMPTY);
        $json = JsonHelper::encode($obj);

        $voteSelection->update([
            'title'  => $request->get('title'),
            'weight' => $request->has('weight') ? $request->get('weight') : 1,
            'data'   => $json,
        ]);

        $afterEdit = $voteSelection->replicate();

        //Log
        LogHelper::info(
            '[VoteSelectionEdited] ' . auth()->user()->email . ' 編輯了選項(Id: ' . $voteSelection->id
            . ', Title: ' . $voteSelection->title . ')',
            '編輯前',
            $beforeEdit->attributesToArray(),
            '編輯後',
            $afterEdit->attributesToArray()
        );

        return redirect()->route('voteEvent.show', $voteSelection->voteEvent)
            ->with('global', '投票選項已更新');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param VoteSelection $voteSelection
     * @return \Illuminate\Http\Response
     */
    public function destroy(VoteSelection $voteSelection)
    {
        if ($voteSelection->voteEvent->isStarted()) {
            return redirect()->route('voteEvent.show', $voteSelection->voteEvent->id)
                ->with('warning', '只能在投票活動開始前編輯選項');
        }
        $voteEvent = $voteSelection->voteEvent;
        //Log
        LogHelper::info(
            '[VoteSelectionDeleted] ' . auth()->user()->email . ' 刪除了選項(Id: ' . $voteSelection->id
            . ', Title: ' . $voteSelection->title . ')',
            $voteSelection->attributesToArray()
        );
        //移除投票選項
        $voteSelection->delete();

        return redirect()->route('voteEvent.show', $voteEvent)
            ->with('global', '投票選項已刪除');
    }

    public function vote(VoteSelection $voteSelection)
    {
        if (!$voteSelection->voteEvent->isInProgress()) {
            return redirect()->route('voteEvent.show', $voteSelection->voteEvent)
                ->with('warning', '非投票期間');
        }
        //檢查投票資格
        if (!$voteSelection->voteEvent->canVote(auth()->user())) {
            return redirect()->route('voteEvent.show', $voteSelection->voteEvent)
                ->with('warning', '不符合投票資格');
        }
        //檢查用戶狀態
        if ($voteSelection->hasVoted(auth()->user())) {
            return redirect()->route('voteEvent.show', $voteSelection->voteEvent)
                ->with('warning', '已投過此項目');
        }
        if ($voteSelection->voteEvent->getMaxSelected()
            <= $voteSelection->voteEvent->getSelectedCount(auth()->user())
        ) {
            return redirect()->route('voteEvent.show', $voteSelection->voteEvent)
                ->with('warning', '無法再投更多項目');
        }
        //新增投票資料
        $voteBallots = VoteBallot::create([
            'user_id'           => auth()->user()->id,
            'vote_selection_id' => $voteSelection->id,
        ]);
        //發現投太多票時，移除最後一票
        if ($voteSelection->voteEvent->getMaxSelected() < $voteSelection->voteEvent->getSelectedCount(auth()->user())) {
            $voteSelectionIdList = $voteSelection->voteEvent->voteSelections->pluck('id')->toArray();
            while ($voteSelection->voteEvent->getMaxSelected()
                < $voteSelection->voteEvent->getSelectedCount(auth()->user())) {
                $voteBallot = VoteBallot::where('user_id', '=', auth()->user()->id)
                    ->whereIn('vote_selection_id', $voteSelectionIdList)
                    ->orderBy('created_at', 'desc')
                    ->first();
                //Log
                LogHelper::info(
                    '[VoteError] ' . auth()->user()->email . ' 投票時出現異常選票，已移除(Id: '
                    . $voteSelection->voteEvent->id . ', Subject: ' . $voteSelection->voteEvent->subject . ')',
                    '選票資料',
                    $voteBallot
                );
                $voteBallot->delete();
            }
        }

        return redirect()->route('voteEvent.show', $voteSelection->voteEvent->id)
            ->with('global', '投票完成');
    }
}
