<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class VoteEvent extends Model
{
    protected $table = 'vote_events';
    protected $fillable = ['open_time', 'close_time', 'subject', 'info', 'max_selected', 'organizer_id', 'show', 'vote_condition', 'show_result'];

    //有效的活動條件，以及說明文字（{value}會自動替換為條件的值）
    static protected $validConditionList = [
        'prefix' => '學號開頭必須是：{value}'
    ];

    public function voteSelections()
    {
        return $this->hasMany('App\VoteSelection')->orderBy('order')->orderBy('id');
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
    public function getSelectedCount(User $user = null)
    {
        if ($user == null) {
            return 0;
        }
        $voteSelectionIdList = $this->voteSelections->lists('id')->toArray();
        $count = $user->voteBallots()->whereIn('vote_selection_id', $voteSelectionIdList)->count();
        return $count;
    }

    public function getHumanTimeString()
    {
        $string = "";
        if ($this->open_time && $this->close_time) {
            $string = $this->getTimeSpanTag($this->open_time) . " ~ " . $this->getTimeSpanTag($this->close_time);
        } else {
            if ($this->open_time) {
                $string = $this->getTimeSpanTag($this->open_time) . ' 起';
            } else if ($this->close_time) {
                $string = '到 ' . $this->getTimeSpanTag($this->close_time) . ' 為止';
            } else {
                $string = "尚未決定";
            }
        }
        return $string;
    }

    public function getTimeSpanTag($time)
    {
        //style="display: inline-block; 是防止字換行
        return '<span title="' . (new Carbon($time))->diffForHumans() . '"  style="display: inline-block;">' . $time . '</span>';
    }

    public function isVisible()
    {
        return $this->isStarted() || $this->show;
    }

    //取得特定條件的值，不存在則回傳空值，而非噴錯。免去在View取值的檢查。
    public function getConditionValue($key)
    {
        //取得條件的json
        $condition = json_decode($this->vote_condition);
        //有需要的值就回傳
        if (!empty($condition->$key)) {
            return $condition->$key;
        }
        //找不到則回傳空值
        return null;
    }

    //檢查是否符合投票資格
    public function canVote($user)
    {
        //未登入
        if (!$user) {
            return false;
        }
        //取得條件的json
        $condition = json_decode($this->vote_condition);
        //此活動無條件限制
        if (empty((array)$condition)) {
            return true;
        }

        //根據定義的條件清單，逐一判斷每個條件，違反任何條件即為不可投票
        foreach (static::$validConditionList as $validCondition => $ignore) {
            if (!$this->checkCondition($user, $validCondition)) {
                return false;
            }
        }

        //若沒有違反任何條件，則表示可投票
        return true;
    }

    //檢查特定條件是否符合
    public function checkCondition($user, $key)
    {
        //未登入
        if (!$user) {
            return false;
        }
        //取得條件的json
        $condition = json_decode($this->vote_condition);
        //此活動無條件限制
        if (empty((array)$condition)) {
            return true;
        }
        //根據不同類型的條件進行檢查
        if ($key == 'prefix') {
            //信箱開頭（學號開頭）
            if (!empty($condition->prefix)) {
                $prefixList = explode(',', $condition->prefix);
                $match = false;
                foreach ($prefixList as $prefix) {
                    if (starts_with($user->email, $prefix)) {
                        $match = true;
                    }
                }
                if (!$match) {
                    return false;
                }
            }
        } elseif ($key == '條件') {
            //不同條件以elseif接下去
        }

        //非有效條件
        return true;
    }

    //取得條件清單，可選擇是否帶有檢查結果（預設為含有結果）
    public function getConditionList($user, $withResult = true)
    {
        $result = [];
        //根據定義的條件清單，逐一判斷每個條件，並列出文字結果
        foreach (static::$validConditionList as $validCondition => $message) {
            if (empty($this->getConditionValue($validCondition))) {
                continue;
            }
            $message = str_replace('{value}', $this->getConditionValue($validCondition), $message);
            if ($user && $withResult) {
                if ($this->checkCondition($user, $validCondition)) {
                    $result[] = '<span style="color: green" title="符合條件">✔</span> ' . $message;
                } else {
                    $result[] = '<span style="color: red" title="不符合條件">✘</span> <b>' . $message . '</b>';
                }
            } else {
                $result[] = $message;
            }
        }
        return $result;
    }

    public function isResultVisible()
    {
        $showResult = $this->show_result;
        if ($showResult == 'always') {
            //總是顯示
            return true;
        } elseif ($showResult == 'after-vote') {
            //投票後可見（遊客則無法看見結果）
            return ($this->getMaxSelected() <= $this->getSelectedCount(Auth::user()));
        } elseif ($showResult == 'after-event') {
            //活動結束後顯示
            return $this->isEnded();
        }
        //錯誤情況，直接不顯示
        return false;
    }
}
