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
    </div>
@endsection
