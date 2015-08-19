<?php

namespace App\Helper;


class JsonHelper
{
    static public function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}
