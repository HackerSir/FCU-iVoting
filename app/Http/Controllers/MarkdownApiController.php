<?php

namespace App\Http\Controllers;

use Hackersir\Helper\MarkdownHelper;
use Illuminate\Http\Request;

class MarkdownApiController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct();
        //
    }

    public function markdownPreview(Request $request)
    {
        //只接受Ajax請求
        if (!$request->ajax()) {
            return 'error';
        }
        $data = $request->getContent();
        //檢查是否有內容
        if (empty($data)) {
            return response()->make(' ');
        }

        return response()->make(MarkdownHelper::translate($data));
    }
}
