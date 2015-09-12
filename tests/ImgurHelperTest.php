<?php


use App\Helper\ImgurHelper;

class ImgurHelperTest extends TestCase
{
    /**
     *
     */
    public function test_getImgurID_function() {
        $this->assertEquals(
            ImgurHelper::getImgurID('http://i.imgur.com/ABCDEF.jpg'),
            'ABCDEF'
        );

        $this->assertEquals(
            ImgurHelper::getImgurID('https://i.imgur.com/ABCDEF.jpg'),
            'ABCDEF'
        );

        $this->assertEquals(
            ImgurHelper::getImgurID('//i.imgur.com/ABCDEF.jpg'),
            'ABCDEF'
        );

        $this->assertEquals(
            ImgurHelper::getImgurID('https://w.imgur.com/ABCDEF.jpg'),
            'ABCDEF'
        );
    }

    /**
     *
     * @depends test_getImgurID_function
     * @return void
     */
    public function test_thumbnail_function() {
        $this->assertEquals(
            ImgurHelper::thumbnail('http://i.imgur.com/'),
            'http://i.imgur.com/'
        );

        $this->assertEquals(
            ImgurHelper::thumbnail('http://i.imgur.com/ABCDEF.jpg'),
            '//i.imgur.com/ABCDEF.jpg'
        );

        $this->assertEquals(
            ImgurHelper::thumbnail('http://i.imgur.com/ABCDEF.jpg', 'l'),
            '//i.imgur.com/ABCDEFl.jpg'
        );

        $this->assertEquals(
            ImgurHelper::thumbnail('http://i.imgur.com/ABCDEF.gif', 'l'),
            '//i.imgur.com/ABCDEFl.gif'
        );

        $this->assertNull(
            ImgurHelper::thumbnail('http://i.imgur.com/ABCDEF.gif', 'a')
        );
    }
}
