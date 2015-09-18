<?php

namespace App\Http\Controllers;


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
        $jobs = DB::table('jobs')->take(5)->get();

        return view('queue-status')->with('jobs', $jobs);
    }
}
