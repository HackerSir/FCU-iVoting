<?php

namespace App\Http\Controllers;

use File;
use Hackersir\Helper\JsonHelper;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function postImage(Request $request)
    {
        //只接受Ajax請求
        if (!$request->ajax()) {
            return 'error';
        }
        //上傳至Imgur
        //取得檔案
        $file = $request->file('image_upload');
        $file = File::get($file->getPathname());
        //檢查client id
        $client_id = env('IMGUR_CLIENT_ID', '');
        if (empty($client_id)) {
            $result = [
                'error' => '請設定Imgur Client ID',
            ];

            return JsonHelper::encode($result);
        }
        //發送請求
        $client = new Client();
        $response = $client->post('https://api.imgur.com/3/image', [
            'timeout' => 30,
            'headers' => [
                'Authorization' => 'Client-ID ' . $client_id,
            ],
            'form_params' => [
                'image' => base64_encode($file),
            ],
            'verify' => false,
        ]);
        $jsonResponse = JsonHelper::decode($response->getBody());
        //取得連結
        $image = $jsonResponse->data->link;
        //回傳詳細資訊
        $result = [
            'success'        => 'success',
            'url'            => $image,
            'initialPreview' => [
                "<img src='$image' class='file-preview-image' title="
                . substr($image, strrpos($image, '/') + 1)
                . " style='max-width:200px;max-height:200px;width:auto;height:auto;'>",
            ],
            'initialPreviewConfig' => [
                [
                    'caption' => substr($image, strrpos($image, '/') + 1),
                    'url'     => route('upload.delete-image'), // server delete action
                    'key'     => $image,
                ],
            ],
        ];

        return JsonHelper::encode($result);
    }

    public function deleteImage(Request $request)
    {
        //目前什麼都不用做，只是需要確保請求發送成功
        //只接受Ajax請求
        if (!$request->ajax()) {
            return 'error';
        }
        $result = [
            'success' => 'success',
        ];

        return JsonHelper::encode($result);
    }
}
