<?php

class StaticPageTest extends TestCase
{
    /**
     * 測試首頁，應該會看到網站名稱
     *
     * @return void
     */
    public function testHomePage()
    {
        $this->visit('/')
            ->see(Config::get('config.sitename'));
    }
}
