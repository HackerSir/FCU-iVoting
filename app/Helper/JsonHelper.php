<?php

namespace App\Helper;

class JsonHelper
{
    public static function isJson($string)
    {
        if (!starts_with($string, '[') && !starts_with($string, '{')) {
            return false;
        }
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public static function encode($value, $options = 0, $depth = 512)
    {
        $options |= JSON_UNESCAPED_UNICODE;     //不跳脫Unicode字元
        $options |= JSON_UNESCAPED_SLASHES;     //不跳脫斜線
        return json_encode($value, $options, $depth);
    }

    public static function decode($json, $assoc = false, $depth = 512, $options = 0)
    {
        return json_decode($json, $assoc, $depth, $options);
    }
}
