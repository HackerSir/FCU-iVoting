<?php

use App\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * 測試票選活動的學號條件限制功能(黑箱測試)
 * User: danny50610
 * Date: 2015/8/11
 * Time: 下午 12:49
 */
class VoteEventConditionTest extends TestCase
{
    use DatabaseTransactions;

    protected $staffUser;
    protected $d02User;
    protected $d03User;
    protected $d04User;

    private function createUser() {
        $this->staffUser = factory('App\User')->create();
        $staff = Role::where('name', '=', 'staff')->first();
        $this->staffUser->attachRole($staff);

        $this->d02User = factory('App\User')->make([
            'email' => 'd0200000@fcu.edu.tw'
        ]);

        $this->d03User = factory('App\User')->make([
            'email' => 'd0300000@fcu.edu.tw'
        ]);

        $this->d04User = factory('App\User')->make([
            'email' => 'd0400000@fcu.edu.tw'
        ]);
    }

    public function test_VoteEventCondition() {
        $this->createUser();

        $subject = str_random(20);
        $voteSelectionId = $this->createVoteEvent($subject);

        $postURL = '/vote-selection/vote/' . $voteSelectionId;

        //關閉 CSRF Protection Middleware
        $this->withoutMiddleware();

        // 清除Session，因為要用彈出訊息來驗證
        $this->flushSession();
        $this->actingAs($this->d03User)
             ->post($postURL)
             ->followRedirects()
             ->dontSee('不符合投票資格')
             ->see('投票完成');

        $this->flushSession();
        $this->actingAs($this->d02User)
             ->post($postURL)
             ->followRedirects()
             ->see('不符合投票資格')
             ->dontSee('投票完成');

        $this->flushSession();
        $this->actingAs($this->d04User)
             ->post($postURL)
             ->followRedirects()
             ->see('不符合投票資格')
             ->dontSee('投票完成');
    }

    protected function createVoteEvent($subject) {
        $this->actingAs($this->staffUser)
            ->visit('/vote-event/create')
            ->see('新增投票活動')
            ->type($subject, 'subject')
            ->type(Carbon::now()->addDay() , 'open_time')
            ->type('d03', 'prefix')
            ->press('新增投票活動');

        $voteEventId = $this->getVoteEventId($subject);

        $this->actingAs($this->staffUser)
             ->visit('/vote-selection/create?vid=' . $voteEventId)
             ->see('新增投票選項')
             ->type('測試選項1', 'title')
             ->press('新增投票選項');

        $this->actingAs($this->staffUser)
            ->visit('/vote-selection/create?vid=' . $voteEventId)
            ->see('新增投票選項')
            ->type('測試選項2', 'title')
            ->press('新增投票選項');

        $this->actingAs($this->staffUser)
            ->visit('/vote-event/' . $voteEventId)
            ->see($subject)
            ->press('立即開始');

        return DB::table('vote_selections')->where('vote_event_id', $voteEventId)->value('id');
    }

    protected function getVoteEventId($subject) {
        return DB::table('vote_events')->where('subject', $subject)->value('id');
    }

    public function test_fun_VoteEvent_checkCondition() {
        $d02User = factory('App\User')->make([
            'email' => 'd0255555@fcu.edu.tw'
        ]);

        $D02User = factory('App\User')->make([
            'email' => 'D0244444@fcu.edu.tw'
        ]);

        $d03User = factory('App\User')->make([
            'email' => 'd0355555@fcu.edu.tw'
        ]);

        $D03User = factory('App\User')->make([
            'email' => 'D0344444@fcu.edu.tw'
        ]);

        $voteEvent = new App\VoteEvent();
        $voteEvent->vote_condition = '{"prefix":"d02"}';

        $this->assertTrue($voteEvent->checkCondition($d02User, 'prefix'));
        $this->assertTrue($voteEvent->checkCondition($D02User, 'prefix'));

        $this->assertFalse($voteEvent->checkCondition($d03User, 'prefix'));
        $this->assertFalse($voteEvent->checkCondition($D03User, 'prefix'));
    }
}
