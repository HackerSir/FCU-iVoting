<?php

namespace App;

use DB;
use App\Helper\JsonHelper;
use Illuminate\Database\Eloquent\Model;

class VoteSelection extends Model
{
    protected $table = 'vote_selections';
    protected $fillable = ['vote_event_id', 'title', 'weight', 'data', 'order'];

    public function voteEvent()
    {
        return $this->belongsTo('App\VoteEvent');
    }

    public function voteBallots()
    {
        return $this->hasMany('App\VoteBallot');
    }

    public function getImageLinksText()
    {
        return ($this->getImageLinks()) ? implode(PHP_EOL, $this->getImageLinks()) : null;
    }

    public function getImageLinks()
    {
        if (!JsonHelper::isJson($this->data)) {
            return [];
        }
        $json = JsonHelper::decode($this->data);
        if (empty($json->image)) {
            return [];
        }
        return $json->image;
    }

    public function getCount()
    {
        $selfCount = $this->voteBallots()
            ->where('vote_selection_id', '=', $this->id)
            ->count();
        return $selfCount;
    }

    public function isMax()
    {
        $maxCount = 0;
        $voteSelections = $this->voteEvent->voteSelections;
        foreach ($voteSelections as $voteSelection) {
            $count = $voteSelection->getCount();
            if ($count > $maxCount) {
                $maxCount = $count;
            }
        }
        //本身得票數
        $selfCount = $this->getCount();
        //判斷
        return $selfCount == $maxCount;
    }

    //取得排名
    public function getRankAttribute()
    {
        $voteSelectionsIdList = VoteSelection::where('vote_event_id', '=', $this->vote_event_id)->lists('id')->toArray();
        $voteBallotList = VoteBallot::select('vote_selection_id', DB::raw('count(*) as total'))
            ->whereIn('vote_selection_id', $voteSelectionsIdList)
            ->groupBy('vote_selection_id')
            ->orderBy(DB::raw('count(vote_selection_id)'), 'desc')
            ->lists('total')
            ->toArray();
        $search = array_search($this->getCount(), $voteBallotList);
        $rank = ($search !== false) ? $search + 1 : count($voteBallotList) + 1;
        return $rank;
    }

    //取得加權分數
    public function getScoreAttribute()
    {
        return $this->getCount() * $this->weight;
    }

    //取得加權排名
    public function getScoreRankAttribute()
    {
        $voteSelectionsIdList = VoteSelection::where('vote_event_id', '=', $this->vote_event_id)->lists('id')->toArray();
        $voteBallotList = VoteBallot::select('vote_selection_id', DB::raw('vote_selection_id, count(*) as total'))
            ->whereIn('vote_selection_id', $voteSelectionsIdList)
            ->groupBy('vote_selection_id')
            ->orderBy(DB::raw('count(vote_selection_id)'), 'desc')
            ->lists('total', 'vote_selection_id')
            ->toArray();
        $voteSelectionsWeight = VoteSelection::where('vote_event_id', '=', $this->vote_event_id)->lists('weight', 'id')->toArray();
        $score = [];
        foreach ($voteSelectionsIdList as $voteSelectionsId) {
            $ballotCount = isset($voteBallotList[$voteSelectionsId]) ? $voteBallotList[$voteSelectionsId] : 0;
            $weight = isset($voteSelectionsWeight[$voteSelectionsId]) ? $voteSelectionsWeight[$voteSelectionsId] : 0;
            $score[$voteSelectionsId] = $ballotCount * $weight;
        }
        rsort($score);  //排序＆去除索引值
        $search = array_search($this->score, $score);
        $rank = ($search !== false) ? $search + 1 : count($score) + 1;
        return $rank;
    }

    public function hasVoted(User $user = null)
    {
        if (!$user) {
            return false;
        }
        $count = $this->voteBallots()->where('user_id', '=', $user->id)->count();
        return $count > 0;
    }

}
