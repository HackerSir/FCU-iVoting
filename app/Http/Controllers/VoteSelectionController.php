<?php

namespace App\Http\Controllers;

use App\VoteEvent;
use App\VoteSelection;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class VoteSelectionController extends Controller
{
    public function __construct()
    {
        //限工作人員
        $this->middleware('staff');
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
                'data' => 'required|max:65535'
            )
        );
        if ($validator->fails()) {
            return Redirect::route('vote-selection.create', ['vid' => $voteEvent->id])
                ->withErrors($validator)
                ->withInput();
        } else {
            $voteSelection = VoteSelection::create(array(
                'vote_event_id' => $voteEvent->id,
                'data' => $request->get('data')
            ));
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
                'data' => 'required|max:65535'
            )
        );
        if ($validator->fails()) {
            return Redirect::route('vote-selection.edit', $id)
                ->withErrors($validator)
                ->withInput();
        } else {
            $voteSelection->data = $request->get('data');
            $voteSelection->save();
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
}
