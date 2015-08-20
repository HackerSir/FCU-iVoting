<?php

namespace App\Helper;


class JsonHelper
{
    static public function isJson($string)
    {
        if (!starts_with($string, '[') && !starts_with($string, '{')) {
            return false;
        }
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}
