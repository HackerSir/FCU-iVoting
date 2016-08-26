<?php

namespace Hackersir;

use DB;
use Hackersir\Helper\JsonHelper;
use Illuminate\Database\Eloquent\Model;

/**
 * Hackersir\VoteSelection
 *
 * @property int $id
 * @property string $title
 * @property int $vote_event_id
 * @property string $data
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $order
 * @property float $weight
 * @property-read \Hackersir\VoteEvent $voteEvent
 * @property-read \Illuminate\Database\Eloquent\Collection|\Hackersir\VoteBallot[] $voteBallots
 * @property-read mixed $rank
 * @property-read mixed $score
 * @property-read mixed $score_rank
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\VoteSelection whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\VoteSelection whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\VoteSelection whereVoteEventId($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\VoteSelection whereData($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\VoteSelection whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\VoteSelection whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\VoteSelection whereOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\VoteSelection whereWeight($value)
 * @mixin \Eloquent
 */
class VoteSelection extends Model
{
    protected $table = 'vote_selections';
    protected $fillable = ['vote_event_id', 'title', 'weight', 'data', 'order'];

    public function voteEvent()
    {
        return $this->belongsTo(VoteEvent::class);
    }

    public function voteBallots()
    {
        return $this->hasMany(VoteBallot::class);
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
        $voteSelectionsIdList = self::where('vote_event_id', '=', $this->vote_event_id)
            ->pluck('id')->toArray();
        $voteBallotList = VoteBallot::select('vote_selection_id', DB::raw('count(*) as total'))
            ->whereIn('vote_selection_id', $voteSelectionsIdList)
            ->groupBy('vote_selection_id')
            ->orderBy(DB::raw('count(vote_selection_id)'), 'desc')
            ->pluck('total')
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
        $voteSelectionsIdList = self::where('vote_event_id', '=', $this->vote_event_id)
            ->pluck('id')->toArray();
        $voteBallotList = VoteBallot::select('vote_selection_id', DB::raw('vote_selection_id, count(*) as total'))
            ->whereIn('vote_selection_id', $voteSelectionsIdList)
            ->groupBy('vote_selection_id')
            ->orderBy(DB::raw('count(vote_selection_id)'), 'desc')
            ->pluck('total', 'vote_selection_id')
            ->toArray();
        $voteSelectionsWeight = self::where('vote_event_id', '=', $this->vote_event_id)
            ->lists('weight', 'id')->toArray();
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
