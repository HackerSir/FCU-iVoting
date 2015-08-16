@extends('app')

@section('main-jumbotron')
    <div class="container">
        <div class="row">
            <div class="col-sm-8 jumbotron">
                <h1>{{ Config::get('config.sitename') }}</h1>

                <p>一個由學生社團做的票選系統，快來參加各種票選活動吧！！！</p>

                <p><a href="{{ URL::route('vote-event.index') }}" class="btn btn-primary btn-lg">查看票選活動 »</a></p>
            </div>
            <div class="col-sm-4 hidden-xs">
                <img src="{{ asset('pic/logo.gif') }}" style="float: right; height: 400px" />
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container container-background">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="text-center">如何投票</h1>
            </div>
        </div>
        <br />
        <div class="row">
            <div class="col-sm-4">
                @if(Auth::guest())
                    <a href="{{ URL::route('member.login') }}" style="color: black;">
                @endif
                        <div class="text-center">
                            <span class="glyphicon glyphicon-log-in text-info" style="font-size: 150px;"></span>
                        </div>
                        <h2 class="text-center">登入票選網站</h2>
                @if(Auth::guest())
                    </a>
                @endif
            </div>
            <div class="col-sm-4">
                <a href="{{ URL::route('vote-event.index') }}" style="color: black;">
                    <div class="text-center">
                        <span class="glyphicon glyphicon-list-alt text-info" style="font-size: 150px;"></span>
                    </div>
                    <h2 class="text-center">瀏覽投票活動</h2>
                </a>
            </div>
            <div class="col-sm-4">
                <div class="text-center">
                    {{-- padding-right: 15px; 是要讓圖案往右移一些，用手機看起來會好點 --}}
                    <span class="glyphicon glyphicon-check text-info" style="font-size: 150px; padding-left: 15px;"></span>
                </div>
                <h2 class="text-center">投下神聖的一票</h2>
            </div>
        </div>
    </div>
@endsection
