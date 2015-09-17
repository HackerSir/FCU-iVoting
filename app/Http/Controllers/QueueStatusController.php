<?php

namespace App\Http\Controllers;


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
        return view('queue-status');
    }
}
