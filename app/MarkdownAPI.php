<?php

namespace App;

use AlfredoRamos\ParsedownExtra\Facades\ParsedownExtra as Markdown;

class MarkdownAPI
{
    static public function translate($string)
    {
        return Markdown::parse(nl2br(htmlspecialchars($string)));
    }
}
