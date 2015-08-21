<?php

namespace App\Http\Controllers;

use App\Helper\LogHelper;
use App\VoteBallot;
use App\VoteEvent;
use App\VoteSelection;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use stdClass;

class VoteSelectionController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        //Email必須驗證
        $this->middleware('email', [
            'only' => [
                'vote'
            ]
        ]);
        //限工作人員
        $this->middleware('role:staff', [
            'except' => [
                'index',
                'show',
                'vote'
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $vid = Input::get('vid');
        if (empty($vid) || !is_numeric($vid)) {
            return Redirect::route('vote-event.index')
                ->with('warning', '請選擇投票活動');
        }
        $voteEvent = VoteEvent::find($vid);
        if ($voteEvent == null) {
            return Redirect::route('vote-event.index')
                ->with('warning', '投票活動不存在');
        }
        if ($voteEvent->isStarted()) {
            return Redirect::route('vote-event.show', $voteEvent->id)
                ->with('warning', '只能在投票活動開始前編輯選項');
        }
        return view('vote.selection.create')->with('voteEvent', $voteEvent);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $vid = $request->get('vid');
        if (empty($vid) || !is_numeric($vid)) {
            return Redirect::route('vote-event.index')
                ->with('warning', '請選擇投票活動');
        }
        $voteEvent = VoteEvent::find($vid);
        if ($voteEvent == null) {
            return Redirect::route('vote-event.index')
                ->with('warning', '投票活動不存在');
        }
        if ($voteEvent->isStarted()) {
            return Redirect::route('vote-event.show', $voteEvent->id)
                ->with('warning', '只能在投票活動開始前編輯選項');
        }
        $validator = Validator::make($request->all(),
            array(
                'title' => 'required|max:65535',
                'image' => 'max:65535'
            )
        );
        if ($validator->fails()) {
            return Redirect::route('vote-selection.create', ['vid' => $voteEvent->id])
                ->withErrors($validator)
                ->withInput();
        } else {
            //封裝JSON
            $obj = new stdClass();
            //$obj->image = explode(PHP_EOL, $request->get('image'));
            $obj->image = preg_split('/(\n|\r|\n\r)/', $request->get('image'), NULL, PREG_SPLIT_NO_EMPTY);
            $json = json_encode($obj, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES);
            $order = ($voteEvent->voteSelections->count() > 0) ? $voteEvent->voteSelections->max('order') + 1 : 0;
            $voteSelection = VoteSelection::create(array(
                'title' => $request->get('title'),
                'vote_event_id' => $voteEvent->id,
                'data' => $json,
                'order' => $order
            ));

            //紀錄
            LogHelper::info(
                '[VoteSelectionCreated] ' .
                Auth::user()->email . ' 為 ' . $voteEvent->subject . ' 建立選項',
                $voteSelection
            );

            return Redirect::route('vote-event.show', $voteSelection->voteEvent->id)
                ->with('global', '投票選項已建立');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        $voteSelection = VoteSelection::find($id);
        if (!$voteSelection) {
            return Redirect::route('vote-event.index')
                ->with('warning', '投票選項不存在');
        }
        if ($voteSelection->voteEvent->isStarted()) {
            return Redirect::route('vote-event.show', $voteSelection->voteEvent->id)
                ->with('warning', '只能在投票活動開始前編輯選項');
        }
        return view('vote.selection.edit')->with('voteSelection', $voteSelection);
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
        $voteSelection = VoteSelection::find($id);
        if (!$voteSelection) {
            return Redirect::route('vote-event.index')
                ->with('warning', '投票選項不存在');
        }
        if ($voteSelection->voteEvent->isStarted()) {
            return Redirect::route('vote-event.show', $voteSelection->voteEvent->id)
                ->with('warning', '只能在投票活動開始前編輯選項');
        }
        $validator = Validator::make($request->all(),
            array(
                'title' => 'required|max:65535',
                'image' => 'max:65535'
            )
        );
        if ($validator->fails()) {
            return Redirect::route('vote-selection.edit', $id)
                ->withErrors($validator)
                ->withInput();
        } else {
            //複製一份，在Log時比較差異
            $beforeEdit = $voteSelection->replicate();

            $voteSelection->title = $request->get('title');
            //封裝JSON
            $obj = new stdClass();
            //$obj->image = explode(PHP_EOL, $request->get('image'));
            $obj->image = preg_split('/(\n|\r|\n\r)/', $request->get('image'), NULL, PREG_SPLIT_NO_EMPTY);
            $json = json_encode($obj, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES);

            $voteSelection->data = $json;
            $voteSelection->save();

            $afterEdit = $voteSelection->replicate();

            //Log
            LogHelper::info(
                '[VoteSelectionEdited] ' . Auth::user()->email . ' 編輯了選項(Id: ' . $voteSelection->id . ', Title: ' . $voteSelection->title . ')',
                "編輯前", $beforeEdit->attributesToArray(),
                "編輯後", $afterEdit->attributesToArray()
            );

            return Redirect::route('vote-event.show', $voteSelection->voteEvent->id)
                ->with('global', '投票選項已更新');
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
        $voteSelection = VoteSelection::find($id);
        if (!$voteSelection) {
            return Redirect::route('vote-event.index')
                ->with('warning', '投票選項不存在');
        }
        if ($voteSelection->voteEvent->isStarted()) {
            return Redirect::route('vote-event.show', $voteSelection->voteEvent->id)
                ->with('warning', '只能在投票活動開始前編輯選項');
        }
        $voteEvent = $voteSelection->voteEvent;
        //移除投票選項
        $voteSelection->delete();
        return Redirect::route('vote-event.show', $voteEvent->id)
            ->with('global', '投票選項已刪除');
    }

    public function vote($id)
    {
        $voteSelection = VoteSelection::find($id);
        if (!$voteSelection) {
            return Redirect::route('vote-event.index')
                ->with('warning', '投票選項不存在');
        }
        if (!$voteSelection->voteEvent->isInProgress()) {
            return Redirect::route('vote-event.show', $voteSelection->voteEvent->id)
                ->with('warning', '非投票期間');
        }
        //檢查投票資格
        if (!$voteSelection->voteEvent->canVote(Auth::user())) {
            return Redirect::route('vote-event.show', $voteSelection->voteEvent->id)
                ->with('warning', '不符合投票資格');
        }
        //檢查用戶狀態
        if ($voteSelection->hasVoted(Auth::user())) {
            return Redirect::route('vote-event.show', $voteSelection->voteEvent->id)
                ->with('warning', '已投過此項目');
        }
        if ($voteSelection->voteEvent->getMaxSelected() <= $voteSelection->voteEvent->getSelectedCount(Auth::user())) {
            return Redirect::route('vote-event.show', $voteSelection->voteEvent->id)
                ->with('warning', '無法再投更多項目');
        }
        //新增投票資料
        $voteBallots = VoteBallot::create(array(
            'user_id' => Auth::user()->id,
            'vote_selection_id' => $voteSelection->id
        ));
        //發現投太多票時，移除最後一票
        if ($voteSelection->voteEvent->getMaxSelected() < $voteSelection->voteEvent->getSelectedCount(Auth::user())) {
            $voteSelectionIdList = $voteSelection->voteEvent->voteSelections->lists('id')->toArray();
            while ($voteSelection->voteEvent->getMaxSelected() < $voteSelection->voteEvent->getSelectedCount(Auth::user())) {
                $voteBallot = VoteBallot::where('user_id', '=', Auth::user()->id)->whereIn('vote_selection_id', $voteSelectionIdList)->orderBy('created_at', 'desc')->first();
                //Log
                LogHelper::info(
                    '[VoteError] ' . Auth::user()->email . ' 投票時出現異常選票，已移除(Id: ' . $voteSelection->voteEvent->id . ', Subject: ' . $voteSelection->voteEvent->subject . ')',
                    '選票資料', $voteBallot
                );
                $voteBallot->delete();
            }
        }

        return Redirect::route('vote-event.show', $voteSelection->voteEvent->id)
            ->with('global', '投票完成');
    }
}
