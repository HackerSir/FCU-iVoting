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
}
