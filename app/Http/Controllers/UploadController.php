<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class UploadController extends Controller
{
    public function postImage(Request $request)
    {
        //只接受Ajax請求
        if (!$request->ajax()) {
            return "error";
        }
        //dd($request->file('image_upload'));
        //TODO upload to imgur
        $image = "http://" . str_random(6);

        $result = [
            'success' => 'success',
            'url' => $image,
            'initialPreview' => [
                "<img src='$image' class='file-preview-image' title=" . substr($image, strrpos($image, '/') + 1) . ">",
            ],
            'initialPreviewConfig' => [[
                'caption' => substr($image, strrpos($image, '/') + 1),
                'url' => URL::route('upload.delete-image'), // server delete action
                'key' => $image
            ]]
        ];
        return json_encode($result);
    }

    public function deleteImage(Request $request)
    {
        //目前什麼都不用做，只是需要確保請求發送成功
        //只接受Ajax請求
        if (!$request->ajax()) {
            return "error";
        }
        $result = [
            'success' => 'success'
        ];
        return json_encode($result);
    }
}
