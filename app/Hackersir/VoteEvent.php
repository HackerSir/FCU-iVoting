<?php

namespace Hackersir;

use Hackersir\Helper\JsonHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * 票選活動
 *
 * @property int $id
 * @property string $open_time
 * @property string $close_time
 * @property string $subject
 * @property string $info
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $max_selected
 * @property int $organizer_id
 * @property bool $show
 * @property string $vote_condition
 * @property string $show_result
 * @property int $award_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Hackersir\VoteSelection[] $voteSelections
 * @property-read \Hackersir\Organizer $organizer
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\VoteEvent whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\VoteEvent whereOpenTime($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\VoteEvent whereCloseTime($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\VoteEvent whereSubject($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\VoteEvent whereInfo($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\VoteEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\VoteEvent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\VoteEvent whereMaxSelected($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\VoteEvent whereOrganizerId($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\VoteEvent whereShow($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\VoteEvent whereVoteCondition($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\VoteEvent whereShowResult($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\VoteEvent whereAwardCount($value)
 * @mixin \Eloquent
 */
class VoteEvent extends Model
{
    protected $table = 'vote_events';

    protected $fillable = [
        'open_time',
        'close_time',
        'subject',
        'info',
        'max_selected',
        'organizer_id',
        'show',
        'vote_condition',
        'show_result',
        'award_count',
    ];

    protected $casts = [
        'open_time',
        'close_time',
    ];

    //有效的活動條件，以及說明文字（{value}會自動替換為條件的值）
    protected static $validConditionList = [
        'prefix' => '學號開頭必須是：{value}',
    ];

    /**
     * @return mixed
     */
    public function voteSelections()
    {
        return $this->hasMany(VoteSelection::class)->orderBy('order')->orderBy('id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function organizer()
    {
        return $this->belongsTo(Organizer::class);
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
        } elseif ($this->max_selected > $this->voteSelections->count()) {
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
        $voteSelectionIdList = $this->voteSelections->pluck('id')->toArray();
        $count = $user->voteBallots()->whereIn('vote_selection_id', $voteSelectionIdList)->count();

        return $count;
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        return $this->isStarted() || $this->show;
    }

    /**
     * 取得特定條件的值，不存在則回傳空值，而非噴錯。免去在View取值的檢查。
     * @param $key
     * @return mixed
     */
    public function getConditionValue($key)
    {
        //取得條件的json
        $condition = JsonHelper::decode($this->vote_condition);
        //有需要的值就回傳
        if (!empty($condition->$key)) {
            return $condition->$key;
        }

        //找不到則回傳空值
        return '';
    }

    /**
     * 檢查 $user 是否符合投票資格
     * @param $user
     * @return bool
     */
    public function canVote($user)
    {
        //未登入
        if (!$user) {
            return false;
        }
        //取得條件的json
        $condition = JsonHelper::decode($this->vote_condition);
        //此活動無條件限制
        if (empty((array) $condition)) {
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

    /**
     * 檢查特定條件是否符合
     * @param $user
     * @param $key
     * @return bool
     */
    public function checkCondition($user, $key)
    {
        //未登入
        if (!$user) {
            return false;
        }
        //取得條件的json
        $condition = JsonHelper::decode($this->vote_condition);
        //此活動無條件限制
        if (empty((array) $condition)) {
            return true;
        }
        //根據不同類型的條件進行檢查
        if ($key == 'prefix') {
            //信箱開頭（學號開頭）
            if (!empty($condition->prefix)) {
                $prefixList = explode(',', $condition->prefix);
                $match = false;
                foreach ($prefixList as $prefix) {
                    //不分大小寫
                    if (starts_with(strtolower($user->email), strtolower($prefix))) {
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

    /**
     * 取得條件清單，可選擇是否帶有檢查結果（預設為含有結果）
     * @param $user
     * @param bool $withResult
     * @return array
     */
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

    /**
     * @return bool
     */
    public function isResultVisible()
    {
        $showResult = $this->show_result;
        if ($showResult == 'always') {
            //總是顯示
            return $this->isStarted();
        } elseif ($showResult == 'after-vote') {
            //完成投票者可看見結果（活動結束後對所有人顯示）
            return $this->isEnded() || $this->getMaxSelected() <= $this->getSelectedCount(auth()->user());
        } elseif ($showResult == 'after-event') {
            //活動結束後顯示
            return $this->isEnded();
        }

        //錯誤情況，直接不顯示
        return false;
    }

    /**
     * 是否先幫使用者隱藏結果，給View使用
     * @return bool
     */
    public function isHideResult()
    {
        //可以顯示結果 and 活動進行中 and 總是顯示 and (未登入 or 登入但未完成投票)

        //必須可以顯示結果＆活動進行中＆總是顯示
        if (!$this->isResultVisible() || !$this->isInProgress() || $this->show_result != 'always') {
            return false;
        }
        //未登入者
        if (!auth()->check()) {
            return true;
        }
        //已完成投票
        if ($this->getMaxSelected() <= $this->getSelectedCount(auth()->user())) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getResultVisibleHintText()
    {
        $showResult = $this->show_result;
        if ($showResult == 'always') {
            return '隨時可以查看票選結果';
        } elseif ($showResult == 'after-vote') {
            return '完成投票者可看見結果（活動結束後對所有人顯示）';
        } elseif ($showResult == 'after-event') {
            return '票選結果將在活動結束後顯示';
        }

        //錯誤情況，直接不顯示
        return '';
    }
}
