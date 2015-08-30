<?php

namespace App\Helper;

use AlfredoRamos\ParsedownExtra\Facades\ParsedownExtra as Markdown;

class MarkdownHelper
{
    static public function translate($string, array $options = [])
    {
        //設定值
        $options = array_merge([
            'autoLineBreak' => true,            //自動換行
            'supportHtml' => false,             //支援HTML
            'openLinkInNewWindow' => true    //以新視窗開啟超連結
        ], $options);

        $result = $string;
        //自動斷行
        if ($options['autoLineBreak']) {
            $result = '';
            //逐行處理
            foreach (preg_split("/((\r?\n)|(\r\n?))/", $string) as $line) {
                //令每行都以兩個空格結尾
                $line = preg_replace('/\s*($)/', '  $1', $line);
                $result .= $line . PHP_EOL;
            }
        }
        //支援html
        if (!$options['supportHtml']) {
            //不支援時，處理html特殊字元
            $result = htmlspecialchars($result);
        }
        //Markdown解析
        $result = Markdown::parse($result);
        //新視窗開啟超連結
        if ($options['openLinkInNewWindow']) {
            $result = preg_replace(
                '/<a[^>]*href="([^"]*)"[^>]*>([^<]*)<\/a>/',
                '<a href=\'$1\' target=\'_blank\'>$2</a>',
                $result
            );
        }
        return $result;
    }
}
