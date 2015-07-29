<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * 測試 "在開始前隱藏投票活動" 功能
 *
 * User: danny50610
 * Date: 2015/7/29
 * Time: 下午 03:20
 */
class HideVoteEventTest extends TestCase
{
    use DatabaseTransactions;

    protected $defaultUser;
    protected $staffUser;

    public function setUp() {
        parent::setUp();

        $this->defaultUser = factory('App\User')->make();
        $this->staffUser = factory('App\User', 'staff')->make();
    }

    /**
     * 1. 新增活動測試：
     *    新增兩個活動，其中一個有勾選 "在開始前隱藏投票活動" 選項
     *    被勾選的活動的 visible 應該為 False
     *
     * @return void
     */
    public function test_CheckBoxOnCreate() {
        $voteEvent_hide_info = array(
            'subject' => str_random(20),
            'open_time' => Carbon::now(),
            'visible' => false,
        );

        $this->createVoteEvent($voteEvent_hide_info);
        $this->seeInDatabase('vote_events', $voteEvent_hide_info);

        $voteEvent_noHide_info = array(
            'subject' => str_random(20),
            'open_time' => Carbon::now(),
            'visible' => true,
        );

        $this->createVoteEvent($voteEvent_noHide_info);
        $this->seeInDatabase('vote_events', $voteEvent_noHide_info);
    }

    protected function createVoteEvent($info) {

        $this->actingAs($this->staffUser)
            ->visit('/vote-event/create')
            ->see('新增投票活動')
            ->type($info['subject'], 'subject');

        if (isset($info['open_time'])) {
            $this->type($info['open_time'] ,'open_time');
        }

        if (!$info['visible']) {
            $this->check('hideVoteEvent');
        }

        $this->press('新增投票活動');
    }

    /**
     * 2. 可見性與訪問測試：
     *    新增四個活動，分別為
     *    a. (   隱藏、活動未開始)
     *    b. (不要隱藏、活動未開始)
     *    c. (   隱藏、活動進行中)
     *    d. (不要隱藏、活動進行中)
     *    e. (   隱藏、活動未開始(沒有開始時間))
     *    f. (不要隱藏、活動未開始(沒有開始時間))
     *
     *    對於一般使用者而言：
     *    他會看到 b, c, d, f
     *    其中 a, e 的網址不可訪問
     *
     *    對於工作人員而言：
     *    他會看到 a, b, c, d, e, f
     *
     * @depends test_CheckBoxOnCreate
     * @return void
     */
    public function test_Visible_and_Access() {
        $voteEvents = array(
            'a' => array(
                'subject' => str_random(20),
                'open_time' => Carbon::now()->addDay(),
                'visible' => false,
            ),
            'b' => array(
                'subject' => str_random(20),
                'open_time' => Carbon::now()->addDay(),
                'visible' => true,
            ),
            'c' => array(
                'subject' => str_random(20),
                'open_time' => Carbon::now()->subDay(),
                'visible' => false,
            ),
            'd' => array(
                'subject' => str_random(20),
                'open_time' => Carbon::now()->subDay(),
                'visible' => true,
            ),
            'e' => array(
                'subject' => str_random(20),
                'visible' => false,
            ),
            'f' => array(
                'subject' => str_random(20),
                'visible' => true,
            ),
        );

        foreach ($voteEvents as $info) {
            $this->createVoteEvent($info);
        }

        $this->actingAs($this->defaultUser)
             ->visit('/vote-event')
             ->dontSee($voteEvents['a']['subject'])
             ->see    ($voteEvents['b']['subject'])
             ->see    ($voteEvents['c']['subject'])
             ->see    ($voteEvents['d']['subject'])
             ->dontSee($voteEvents['e']['subject'])
             ->see    ($voteEvents['f']['subject']);

        $this->assertEquals(302, $this->call('GET', '/vote-event/' . $this->getVoteEventId($voteEvents['a']))->status());
        $this->assertEquals(200, $this->call('GET', '/vote-event/' . $this->getVoteEventId($voteEvents['b']))->status());
        $this->assertEquals(200, $this->call('GET', '/vote-event/' . $this->getVoteEventId($voteEvents['c']))->status());
        $this->assertEquals(200, $this->call('GET', '/vote-event/' . $this->getVoteEventId($voteEvents['d']))->status());
        $this->assertEquals(302, $this->call('GET', '/vote-event/' . $this->getVoteEventId($voteEvents['e']))->status());
        $this->assertEquals(200, $this->call('GET', '/vote-event/' . $this->getVoteEventId($voteEvents['f']))->status());

        $this->actingAs($this->staffUser)
             ->visit('/vote-event')
             ->see($voteEvents['a']['subject'])
             ->see($voteEvents['b']['subject'])
             ->see($voteEvents['c']['subject'])
             ->see($voteEvents['d']['subject'])
             ->see($voteEvents['e']['subject'])
             ->see($voteEvents['f']['subject']);

        $this->assertEquals(200, $this->call('GET', '/vote-event/' . $this->getVoteEventId($voteEvents['a']))->status());
        $this->assertEquals(200, $this->call('GET', '/vote-event/' . $this->getVoteEventId($voteEvents['b']))->status());
        $this->assertEquals(200, $this->call('GET', '/vote-event/' . $this->getVoteEventId($voteEvents['c']))->status());
        $this->assertEquals(200, $this->call('GET', '/vote-event/' . $this->getVoteEventId($voteEvents['d']))->status());
        $this->assertEquals(200, $this->call('GET', '/vote-event/' . $this->getVoteEventId($voteEvents['e']))->status());
        $this->assertEquals(200, $this->call('GET', '/vote-event/' . $this->getVoteEventId($voteEvents['f']))->status());
    }

    protected function getVoteEventId($info) {
        return DB::table('vote_events')->where('subject', $info['subject'])->value('id');
    }

    /**
     * 3. 選項變更測試：
     *    新增四個活動，分別為
     *    a. (   隱藏、活動未開始)
     *    b. (不要隱藏、活動未開始)
     *    c. (   隱藏、活動進行中)
     *    d. (不要隱藏、活動進行中)
     *
     *    嘗試對每個選項變更成相反狀態
     *    其中 a, b 狀態會相反
     *    c, d 不變
     *
     * @depends test_Visible_and_Access
     * @return void
     */
    public function test_ChangeVisible() {
        $voteEvents = array(
            'a' => array(
                'subject' => str_random(20),
                'open_time' => Carbon::now()->addDay(),
                'visible' => false,
            ),
            'b' => array(
                'subject' => str_random(20),
                'open_time' => Carbon::now()->addDay(),
                'visible' => true,
            ),
            'c' => array(
                'subject' => str_random(20),
                'open_time' => Carbon::now()->subDay(),
                'visible' => false,
            ),
            'd' => array(
                'subject' => str_random(20),
                'open_time' => Carbon::now()->subDay(),
                'visible' => true,
            ),
        );

        foreach ($voteEvents as $value) {
            $this->createVoteEvent($value);
            $value['id'] = $this->getVoteEventId($value);

            $this->reverseVisible($value);

            if (Carbon::now() < $value['open_time']) {
                //活動未開始
                $this->assertEquals(
                    ! $value['visible'],
                    boolval(DB::table('vote_events')->where('subject', $value['subject'])->value('visible'))
                );
            }
            else {
                //活動進行中
                $this->assertEquals(
                    $value['visible'],
                    boolval(DB::table('vote_events')->where('subject', $value['subject'])->value('visible'))
                );
            }
        }

    }

    protected function reverseVisible($info) {
        $this->actingAs($this->staffUser)
             ->visit('/vote-event/' . $info['id'] . '/edit')
             ->see('編輯投票活動');

        $form = $this->crawler->filter('form')->form();

        if ($info['visible']) {
            $form['hideVoteEvent']->tick();
        }
        else {
            $form['hideVoteEvent']->untick();
        }
        $this->makeRequestUsingForm($form);
    }

}
