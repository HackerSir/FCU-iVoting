<?php

namespace App\Helper;


class ImgurHelper
{
    static public function getImgurID($url)
    {
        $pattern = '/^https?:\/\/[iw\.]*imgur\.[^\/]*\/(?:gallery\/)?([^\?\s\.]*).*$/im';
        preg_match($pattern, $url, $matches);
        if (empty($matches) || count($matches) < 2) {
            return null;
        }
        return $matches[1];
    }

    static public function thumbnail($url, $suffix = null)
    {
        if (is_null(self::getImgurID($url))) {
            return $url;
        }
        if (!is_null($suffix) && !in_array($suffix, ['s', 'b', 't', 'm', 'l', 'h'])) {
            return null;
        }
        $thumbnail = "https://i.imgur.com/" . self::getImgurID($url) . $suffix . ".jpg";
        return $thumbnail;
    }
}
