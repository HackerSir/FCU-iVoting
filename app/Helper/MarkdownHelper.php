<?php

namespace App\Helper;

use AlfredoRamos\ParsedownExtra\Facades\ParsedownExtra as Markdown;

class MarkdownHelper
{
    static public function translate($string)
    {
        $result = "";
        //逐行處理
        foreach (preg_split("/((\r?\n)|(\r\n?))/", $string) as $line) {
            //令每行都以兩個空格結尾
            $line = preg_replace('/\s*($)/', '  $1', $line);
            $result .= $line . PHP_EOL;
        }
        $result = Markdown::parse(htmlspecialchars($result));
        return $result;
    }
}
