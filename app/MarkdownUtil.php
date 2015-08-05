<?php

namespace App;

use AlfredoRamos\ParsedownExtra\Facades\ParsedownExtra as Markdown;

class MarkdownUtil
{
    static public function translate($string)
    {
        return self::autoNewline(Markdown::parse(htmlspecialchars($string)));
    }

    static private function autoNewline($string)
    {
        $string = preg_replace('/<br \/>/', PHP_EOL, $string);
        $string = nl2br($string);
        return $string;
    }
}
