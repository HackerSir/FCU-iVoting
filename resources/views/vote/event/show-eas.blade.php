@extends('app')

@section('title')
    {{ $voteEvent->subject }} - 投票活動
@endsection

@section('content')
    <div class="container">
        @if(Auth::check() && Auth::user()->isStaff())
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <h3 class="panel-title">控制面板</h3>
                </div>
                <div class="panel-body">
                    @if(!$voteEvent->isEnded())
                        {!! HTML::linkRoute('vote-event.edit', '編輯投票活動', $voteEvent->id, ['class' => 'btn btn-info']) !!}
                    @endif
                    @if(!$voteEvent->isStarted())
                        {!! Form::open(['route' => ['vote-event.destroy', $voteEvent->id], 'style' => 'display: inline', 'method' => 'DELETE',
                        'onSubmit' => "return confirm('確定要刪除投票活動嗎？');"]) !!}
                        {!! Form::submit('刪除', ['class' => 'btn btn-danger']) !!}
                        {!! Form::close() !!}
                    @endif
                    @if(Auth::user()->isAdmin())
                        @if(Request::url() != $autoRedirectSetting->data)
                            {!! Form::open(['route' => ['setting.update', $autoRedirectSetting->id], 'style' => 'display: inline' ,'class' => 'form-horizontal', 'method' => 'PUT',
                            'onSubmit' => "return confirm('確定要將此活動設為預設頁面嗎？');"]) !!}
                            {!! Form::hidden('data', Request::url()) !!}
                            {!! Form::submit('設為預設', ['class' => 'btn btn-danger', 'title' => '設為進入網站時預設導向之頁面']) !!}
                            {!! Form::close() !!}
                        @else
                            {!! Form::open(['route' => ['setting.update', $autoRedirectSetting->id], 'style' => 'display: inline' ,'class' => 'form-horizontal', 'method' => 'PUT',
                            'onSubmit' => "return confirm('確定不在將此活動作為預設頁面嗎？');"]) !!}
                            {!! Form::hidden('data', "") !!}
                            {!! Form::submit('取消預設', ['class' => 'btn btn-default', 'title' => '取消網站預設導向頁面之設定']) !!}
                            {!! Form::close() !!}
                        @endif
                    @endif
                    {{-- 控制開票事件狀態 --}}
                    @if($voteEvent->isEnded())
                        {{-- Do nothing. To maintain logic --}}
                    @elseif($voteEvent->isInProgress())
                        @if(Auth::check() && Auth::user()->isStaff())
                            {!! Form::open(['route' => ['vote-event.end', $voteEvent->id], 'style' => 'display: inline', 'method' => 'POST',
                            'onSubmit' => "return confirm('確定要立即結束此投票活動嗎？');"]) !!}
                            {!! Form::submit('立即結束', ['class' => 'btn btn-danger']) !!}
                            {!! Form::close() !!}
                        @endif
                    @else
                        @if(Auth::check() && Auth::user()->isStaff())
                            {!! Form::open(['route' => ['vote-event.start', $voteEvent->id], 'style' => 'display: inline', 'method' => 'POST',
                            'onSubmit' => "return confirm('確定要立即開始此投票活動嗎？');"]) !!}
                            {!! Form::submit('立即開始', ['class' => 'btn btn-danger']) !!}
                            {!! Form::close() !!}
                        @endif
                    @endif
                </div>
            </div>
        @endif

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
