<?php


use Hackersir\Helper\ImgurHelper;

class ImgurHelperTest extends TestCase
{
    /**
     * 測試 ImgurHelper::getImgurID function
     */
    public function test_getImgurID_function()
    {
        //測試 http:// 開頭
        $this->assertEquals('ABCDEF', ImgurHelper::getImgurID('http://i.imgur.com/ABCDEF.jpg'));
        //測試 https:// 開頭
        $this->assertEquals('ABCDEF', ImgurHelper::getImgurID('https://i.imgur.com/ABCDEF.jpg'));
        //測試 // 開頭
        $this->assertEquals('ABCDEF', ImgurHelper::getImgurID('//i.imgur.com/ABCDEF.jpg'));
        //測試 w.imgur.com
        $this->assertEquals('ABCDEF', ImgurHelper::getImgurID('https://w.imgur.com/ABCDEF.jpg'));
    }

    /**
     * 測試 ImgurHelper::thumbnail function
     *
     * @depends test_getImgurID_function
     * @return void
     */
    public function test_thumbnail_function()
    {
        //測試 錯誤網址
        $this->assertEquals(
            'http://i.imgur.com/',
            ImgurHelper::thumbnail('http://i.imgur.com/')
        );
        //測試 正常網址
        $this->assertEquals(
            '//i.imgur.com/ABCDEF.jpg',
            ImgurHelper::thumbnail('http://i.imgur.com/ABCDEF.jpg')
        );
        //測試 縮圖功能
        $this->assertEquals(
            '//i.imgur.com/ABCDEFl.jpg',
            ImgurHelper::thumbnail('http://i.imgur.com/ABCDEF.jpg', 'l')
        );
        //測試 副檔名保留
        $this->assertEquals(
            '//i.imgur.com/ABCDEFl.gif',
            ImgurHelper::thumbnail('http://i.imgur.com/ABCDEF.gif', 'l')
        );
        //測試 縮圖功能，但是縮圖參數錯誤
        $this->assertNull(
            ImgurHelper::thumbnail('http://i.imgur.com/ABCDEF.gif', 'a')
        );
    }
}
