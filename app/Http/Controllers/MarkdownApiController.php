<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\MarkdownUtil;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class MarkdownApiController extends Controller
{

    /**
     * Create a new controller instance.
     *
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
            return "error";
        }
        $data = $request->getContent();
        //檢查是否有內容
        if (empty($data)) {
            return Response::make(" ");
        }
        return Response::make(MarkdownUtil::translate($data));
    }
}
