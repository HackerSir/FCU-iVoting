@extends('app')

@section('css')
    <style type="text/css">
        body {
            padding-top: 50px !important;
        }
    </style>
@endsection

@section('main-jumbotron')
    <div class="jumbotron" style="padding-top: 64px;">
        <div class="container">
            <h1>逢甲票選系統</h1>

            <p>一個由學生社團做的票選系統，快來參加各種票選活動吧！！！</p>

            <p><a href="{{ URL::route('vote-event.index') }}" class="btn btn-primary btn-lg">查看票選活動 »</a></p>
        </div>
    </div>
@endsection
