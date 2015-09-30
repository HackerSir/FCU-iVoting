<?php

namespace App\Http\Controllers;

use DOMDocument;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class QueueStatusController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        //限管理員
        $this->middleware('role:admin');
    }

    //主頁面
    public function index()
    {
        $queues = [];
        $bodyStr = '';

        try {
            $client = new Client();
            $res = $client->request('GET', 'http://localhost:9001');

            if ($res->getStatusCode() == 200) {
                $bodyStr = $res->getBody();
            } else {
                $queues = '無法取得Queue狀態資料(code: ' . $res->getStatusCode() . ')';
            }
        } catch (Exception $e) {
            $queues = '無法取得Queue狀態資料';
        }

        if ($bodyStr != '') {
            try {
                $DOM = new DOMDocument;
                $DOM->loadHTML($bodyStr);

                $rows = $DOM->getElementsByTagName('tbody')[0]->getElementsByTagName('tr');

                for ($i = 0; $i < $rows->length; $i++) {
                    $cols = $rows[$i]->getElementsByTagName('td');
                    $queue = [];
                    $queue['state'] = $cols[0]->getElementsByTagName('span')[0]->nodeValue;

                    $description = $cols[1]->getElementsByTagName('span')[0]->nodeValue;
                    $queue['description'] = substr($description, strpos($description, ',') + 2);

                    $queue['name'] = $cols[2]->getElementsByTagName('a')[0]->nodeValue;

                    array_push($queues, $queue);
                }
            } catch (Exception $e) {
                $queues = '解析Queue狀態資料發生錯誤';
            }
        }

        $jobs = DB::table('jobs')->take(5)->get();

        $failedJobs = DB::table('failed_jobs')->get();

        return view('queue-status')
            ->with('jobs', $jobs)
            ->with('failedJobs', $failedJobs)
            ->with('queues', $queues);
    }
}
