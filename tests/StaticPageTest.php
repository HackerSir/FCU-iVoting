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

    public function testPrivacyPage()
    {
        $this->visit('/policies/privacy')
            ->see('「' . Config::get('config.sitename') . '」隱私權政策');
    }

    public function testTermsPage()
    {
        $this->visit('/policies/terms')
            ->see('「' . Config::get('config.sitename') . '」服務條款');
    }

    public function testFAQPage()
    {
        $this->visit('/policies/FAQ')
            ->see('「' . Config::get('config.sitename') . '」常見問題');
    }
}
