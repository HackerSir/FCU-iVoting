<?php

namespace Hackersir\Helper;

class ImgurHelper
{
    public static function getImgurID($url)
    {
        $pattern = '/^(?:(?:https?:)?\/\/)?[iw\.]*imgur\.[^\/]*\/(?:gallery\/)?([^\?\s\.]*).*$/im';
        preg_match($pattern, $url, $matches);
        if (empty($matches) || count($matches) < 2) {
            return;
        }

        return $matches[1];
    }

    public static function thumbnail($url, $suffix = null)
    {
        if (empty(self::getImgurID($url))) {
            return $url;
        }
        if (!empty($suffix) && !in_array($suffix, ['s', 'b', 't', 'm', 'l', 'h'])) {
            return;
        }
        //取得附檔名
        $extensionPattern = '/[^\\\\]*\.(\w+)$/';
        preg_match($extensionPattern, $url, $matches);
        $extension = $matches[1];
        $thumbnail = '//i.imgur.com/' . self::getImgurID($url) . $suffix . '.' . $extension;

        return $thumbnail;
    }
}
