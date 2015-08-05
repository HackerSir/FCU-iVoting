<?php

namespace App;

use AlfredoRamos\ParsedownExtra\Facades\ParsedownExtra as Markdown;

class MarkdownAPI
{
    static public function translate($string)
    {
        return nl2br(Markdown::parse(htmlspecialchars($string)));
    }
}
