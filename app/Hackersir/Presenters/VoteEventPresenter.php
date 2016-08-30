<?php

namespace Hackersir\Presenters;

use Hackersir\VoteEvent;

class VoteEventPresenter
{
    /**
     * 取得活動狀態的 label
     * @param VoteEvent $voteEvent
     * @return string
     */
    public function getStatusLabel(VoteEvent $voteEvent)
    {
        $label = '';
        if ($voteEvent->isEnded()) {
            $label = '<span class="label label-warning label-adjust">已結束</span>';
        } elseif ($voteEvent->isInProgress()) {
            $label = '<span class="label label-success label-adjust">進行中</span>';
        } else {
            $label = '<span class="label label-default label-adjust">未開始</span>';
        }

        return $label;
    }

    /**
     * @return string
     */
    public function getHumanTimeString(VoteEvent $voteEvent)
    {
        $string = '';
        if ($voteEvent->open_time && $voteEvent->close_time) {
            $string = $this->getTimeSpanTag($voteEvent->open_time) . ' ~ ' . $this->getTimeSpanTag($voteEvent->close_time);
        } else {
            if ($voteEvent->open_time) {
                $string = $this->getTimeSpanTag($voteEvent->open_time) . ' 起';
            } elseif ($voteEvent->close_time) {
                $string = '到 ' . $this->getTimeSpanTag($voteEvent->close_time) . ' 為止';
            } else {
                $string = '尚未決定';
            }
        }

        return $string;
    }

    /**
     * @param $time
     * @return string
     */
    public function getTimeSpanTag($time)
    {
        //style="display: inline-block; 是防止字換行
        return '<span style="display: inline-block;">' . $time . '</span>';
    }
}
