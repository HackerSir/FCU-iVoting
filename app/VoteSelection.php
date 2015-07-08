<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VoteSelection extends Model
{
    protected $table = 'vote_selections';
    protected $fillable = ['vote_event_id', 'data'];

    public function voteEvent()
    {
        return $this->belongsTo('App\VoteEvent');
    }

    public function voteBallots()
    {
        return $this->hasMany('App\VoteBallot');
    }

    public function getTitle()
    {
        if (!JSON::isJson($this->data)) {
            return $this->data;
        }
        $json = json_decode($this->data);
        if (empty($json->title)) {
            return $this->data;
        }
        return $json->title;
    }

    public function getImageLinksText()
    {
        return ($this->getImageLinks()) ? implode(PHP_EOL, $this->getImageLinks()) : null;
    }

    public function getImageLinks()
    {
        if (!JSON::isJson($this->data)) {
            return [];
        }
        $json = json_decode($this->data);
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

    public function hasVoted($user)
    {
        $count = $this->voteBallots()->where('user_id', '=', $user->id)->count();
        return $count > 0;
    }

}
