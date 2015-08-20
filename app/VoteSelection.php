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

    /** 利用Eloquent的Accessors & Mutators，可用$var->title取值或賦值
     * 參考：http://laravel.tw/docs/5.1/eloquent-mutators#accessors-and-mutators
     */
    public function getTitleAttribute()
    {
        //如果有標題屬性，直接取標題
        if (!empty($this->attributes['title'])) {
            return $this->attributes['title'];
        }
        //以下為向下相容，自動更新舊資料
        //否則嘗試從data解析標題
        if (JsonHelper::isJson($this->data)) {
            $json = json_decode($this->data);
            if (!empty($json->title)) {
                //找出並移除data中的title
                $this->attributes['title'] = $json->title;
                unset($json->title);
                $this->data = json_encode($json, JSON_UNESCAPED_UNICODE);
                $this->save();
            }
        }
        return $this->attributes['title'];
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

    public function hasVoted(User $user = null)
    {
        if (!$user) {
            return false;
        }
        $count = $this->voteBallots()->where('user_id', '=', $user->id)->count();
        return $count > 0;
    }

}
