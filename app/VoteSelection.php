<?php

namespace App;

use App\Helper\JsonHelper;
use Illuminate\Database\Eloquent\Model;

class VoteSelection extends Model
{
    protected $table = 'vote_selections';
    protected $fillable = ['vote_event_id', 'title', 'data', 'order'];

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

    public function hasVoted(User $user = null)
    {
        if (!$user) {
            return false;
        }
        $count = $this->voteBallots()->where('user_id', '=', $user->id)->count();
        return $count > 0;
    }

}
