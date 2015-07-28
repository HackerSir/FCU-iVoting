<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class VoteEvent extends Model
{
    protected $table = 'vote_events';
    protected $fillable = ['open_time', 'close_time', 'subject', 'info', 'max_selected', 'organizer_id', 'visible'];

    public function voteSelections()
    {
        return $this->hasMany('App\VoteSelection');
    }

    public function organizer()
    {
        return $this->belongsTo('App\Organizer');
    }

    public function isStarted()
    {
        if (empty($this->open_time)) {
            return false;
        }
        $open_time = new Carbon($this->open_time);
        if (Carbon::now()->gte($open_time)) {
            return true;
        }
        return false;
    }

    public function isEnded()
    {
        if (empty($this->close_time)) {
            return false;
        }
        if (!$this->isStarted()) {
            return false;
        }
        $close_time = new Carbon($this->close_time);
        if (Carbon::now()->gte($close_time)) {
            return true;
        }
        return false;
    }

    public function isInProgress()
    {
        return $this->isStarted() && !$this->isEnded();
    }

    public function getMaxSelected()
    {
        if ($this->max_selected < 1) {
            $max_selected = 1;
        } else if ($this->max_selected > $this->voteSelections->count()) {
            $max_selected = $this->voteSelections->count();
        } else {
            $max_selected = $this->max_selected;
        }
        return $max_selected;
    }

    //特定用戶在此活動選擇之選項數量
    public function getSelectedCount($user)
    {
        $voteSelectionIdList = $this->voteSelections->lists('id')->toArray();
        $count = $user->voteBallots()->whereIn('vote_selection_id', $voteSelectionIdList)->count();
        return $count;
    }

    public function getHumanTimeString()
    {
        $string = "";
        if ($this->open_time && $this->close_time) {
            $string = $this->open_time . " ~ " . $this->close_time;
        } else {
            if ($this->open_time) {
                $string = $this->open_time . " 起";
            } else if ($this->close_time) {
                $string = "到 " . $this->close_time . " 為止";
            } else {
                $string = "尚未決定";
            }
        }
        return $string;
    }

    public function isVisible() {
        return $this->isStarted() || $this->visible;
    }
}
