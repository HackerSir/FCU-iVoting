@extends('app')

@section('title')
    {{ $voteEvent->subject }} - 投票活動
@endsection

@section('content')
    <div class="container">
        <div class="jumbotron">
            <h1>{{ $voteEvent->subject }}</h1>

            <p>{!! Markdown::parse(htmlspecialchars($voteEvent->info)) !!}</p>

            <p>活動期間：{{ $voteEvent->open_time }} &nbsp; ~ &nbsp; {{ $voteEvent->close_time }}</p>
        </div>

        <div class="bs-callout bs-callout-warning">
            <h4>投票規則</h4>
            <ul>
                <li>每人最多可以投&nbsp;{{ $voteEvent->max_selected }}&nbsp;票<br/></li>
                <li>採相對多數決(也就是最高票獲選)</li>
            </ul>
        </div>
    </div>
@endsection
