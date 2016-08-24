<?php

namespace App\Http\Controllers;

use DB;
use DOMDocument;
use Exception;
use GuzzleHttp\Client;
use Log;

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
            $count = 0;
            $bodyStr = preg_replace_callback(
                '/name="([^"]*)"/',
                function ($matches) use (&$count) {
                    $count++;

                    return 'name="' . $matches[1] . $count . '"';
                },
                $bodyStr
            );

            try {
                $DOM = new DOMDocument;
                $DOM->loadHTML($bodyStr);

                $rows = $DOM->getElementsByTagName('tbody')->item(0)->getElementsByTagName('tr');

                for ($i = 0; $i < $rows->length; $i++) {
                    $cols = $rows->item($i)->getElementsByTagName('td');
                    $queue = [];
                    $queue['state'] = $cols->item(0)->getElementsByTagName('span')->item(0)->nodeValue;

                    $description = $cols->item(1)->getElementsByTagName('span')->item(0)->nodeValue;
                    $queue['description'] = substr($description, strpos($description, ',') + 2);

                    $queue['name'] = $cols->item(2)->getElementsByTagName('a')->item(0)->nodeValue;

                    array_push($queues, $queue);
                }
            } catch (Exception $e) {
                $queues = '解析Queue狀態資料發生錯誤';
                Log::error($e);
            }
        }

        $jobs = DB::table('jobs')->take(5)->get();
        $failedJobs = DB::table('failed_jobs')->get();

        $jobCount = DB::table('jobs')->count();
        $failedJobCount = DB::table('failed_jobs')->count();

        return view('queue-status')
            ->with('jobs', $jobs)
            ->with('failedJobs', $failedJobs)
            ->with('jobCount', $jobCount)
            ->with('failedJobCount', $failedJobCount)
            ->with('queues', $queues);
    }
}
