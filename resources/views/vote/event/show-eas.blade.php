@extends('app')

@section('title')
    {{ $voteEvent->subject }} - 投票活動
@endsection

@section('content')
    <div class="container">
        <div class="jumbotron">
            <h1>
                {{ $voteEvent->subject }}
                <span class="label label-success" style="font-size: 70%; margin-left: 10px; position: relative; top: -5px;">
                    @if($voteEvent->isEnded())
                        已結束
                    @elseif($voteEvent->isInProgress())
                        進行中
                    @else
                        未開始
                    @endif
                </span>
            </h1>

            <p>{!! Markdown::parse(htmlspecialchars($voteEvent->info)) !!}</p>

            <p>活動期間：{{ $voteEvent->open_time }} &nbsp; ~ &nbsp; {{ $voteEvent->close_time }}</p>

            {!! HTML::linkRoute('vote-event.index', '返回投票活動列表', [], ['class' => 'btn btn-default pull-right']) !!}
        </div>

        <div class="bs-callout bs-callout-warning">
            <h4>投票規則</h4>
            <ul>
                <li>每人最多可以投&nbsp;{{ $voteEvent->getMaxSelected() }}&nbsp;票
                    @if(Auth::check() && Auth::user()->isStaff())
                        （設定值：{{ $voteEvent->max_selected }}）
                    @endif
                </li>
                <li>採相對多數決(也就是最高票獲選)</li>
            </ul>
        </div>
    </div>
@endsection
