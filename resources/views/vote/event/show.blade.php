@extends('app')

@section('title')
    {{ $voteEvent->subject }} - 投票活動
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">{{ $voteEvent->subject }} - 投票活動</div>
                    {{-- Panel body --}}
                    <div class="panel-body">
                        <div class="row">
                            <div class="text-center col-md-12 col-md-offset-0">
                                <table class="table table-hover">
                                    <tr>
                                        <td class="col-md-2">投票主題：</td>
                                        <td>
                                            {{ $voteEvent->subject }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>狀態：</td>
                                        <td>
                                            @if($voteEvent->isEnded())
                                                已結束
                                            @elseif($voteEvent->isInProgress())
                                                進行中
                                                @if(Auth::check() && Auth::user()->isStaff())
                                                    <br />
                                                    {!! Form::open(['route' => ['vote-event.end', $voteEvent->id], 'style' => 'display: inline', 'method' => 'POST',
                                                    'onSubmit' => "return confirm('確定要立即結束此投票活動嗎？');"]) !!}
                                                    {!! Form::submit('立即結束', ['class' => 'btn btn-danger']) !!}
                                                    {!! Form::close() !!}
                                                @endif
                                            @else
                                                未開始
                                                @if(Auth::check() && Auth::user()->isStaff())
                                                    <br />
                                                    {!! Form::open(['route' => ['vote-event.start', $voteEvent->id], 'style' => 'display: inline', 'method' => 'POST',
                                                    'onSubmit' => "return confirm('確定要立即開始此投票活動嗎？');"]) !!}
                                                    {!! Form::submit('立即開始', ['class' => 'btn btn-danger']) !!}
                                                    {!! Form::close() !!}
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>開始時間：</td>
                                        <td>{{ $voteEvent->open_time }}</td>
                                    </tr>
                                    <tr>
                                        <td>結束時間：</td>
                                        <td>{{ $voteEvent->close_time }}</td>
                                    </tr>
                                    <tr>
                                        <td>最大數量：</td>
                                        <td>設定：{{ $voteEvent->max_selected }}<br />
                                        實際：{{ $voteEvent->getMaxSelected() }}</td>
                                    </tr>
                                </table>
                                <hr />
                                <div class="text-left">
                                    {!! Markdown::parse(htmlspecialchars($voteEvent->info)) !!}
                                </div>
                                <hr />
                                <div>
                                    @if(Auth::check() && Auth::user()->isStaff())
                                        @if(!$voteEvent->isEnded())
                                            {!! HTML::linkRoute('vote-event.edit', '編輯投票活動', $voteEvent->id, ['class' => 'btn btn-primary']) !!}
                                        @endif
                                        {!! HTML::linkRoute('vote-event.index', '返回投票活動列表', [], ['class' => 'btn btn-default']) !!}
                                        @if(!$voteEvent->isStarted())
                                            {!! Form::open(['route' => ['vote-event.destroy', $voteEvent->id], 'style' => 'display: inline', 'method' => 'DELETE',
                                            'onSubmit' => "return confirm('確定要刪除投票活動嗎？');"]) !!}
                                            {!! Form::submit('刪除', ['class' => 'btn btn-danger']) !!}
                                            {!! Form::close() !!}
                                        @endif
                                        @if($voteEvent->isInProgress())
                                            {!! HTML::linkRoute('vote-event.show', '進入投票頁面', ['id' => $voteEvent->id], ['class' => 'btn btn-primary']) !!}
                                        @endif
                                    @else
                                        {!! HTML::linkRoute('vote-event.index', '返回投票活動列表', [], ['class' => 'btn btn-default']) !!}
                                        @if(Auth::user()->group->name == 'vote' && $voteEvent->isInProgress())
                                            {!! HTML::linkRoute('vote-event.show', '進入投票頁面', ['id' => $voteEvent->id], ['class' => 'btn btn-primary']) !!}
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">投票選項</div>
                    {{-- Panel body --}}
                    <div class="panel-body">
                        <div class="row">
                            <div class="text-center col-md-12 col-md-offset-0">
                                @if(Auth::check() && Auth::user()->isStaff() && !$voteEvent->isStarted())
                                    {!! HTML::linkRoute('vote-selection.create', '新增投票選項', ['vid' => $voteEvent->id], ['class' => 'btn btn-primary pull-right']) !!}
                                @endif
                                <table class="table table-hover">
                                    @if(count($voteEvent->voteSelections))
                                        <thead>
                                            <tr>
                                                @if(Auth::check() && Auth::user()->isStaff() && !$voteEvent->isStarted())
                                                    <th class="col-md-8 text-center">投票項目</th>
                                                    <th class="col-md-4"></th>
                                                @elseif($voteEvent->isEnded())
                                                    <th class="col-md-2 text-center">最高票</th>
                                                    <th class="col-md-8 text-center">投票項目</th>
                                                    <th class="col-md-2 text-center">票數</th>
                                                @else
                                                    <th class="col-md-12 text-center">投票項目</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($voteEvent->voteSelections as $voteSelectionItem)
                                                <tr>
                                                    @if($voteEvent->isEnded())
                                                        <td class="col-md-2">
                                                            @if($voteSelectionItem->isMax())
                                                                <span title="最高票">♛</span>
                                                            @endif
                                                        </td>
                                                    @endif
                                                    <td>
                                                        @if($voteSelectionItem->card != null)
                                                            {{ $voteSelectionItem->card->getName() }}
                                                            <a href="{{ URL::route('card.show', $voteSelectionItem->card->id) }}" title="卡片資料"><i class="glyphicon glyphicon-credit-card"></i></a>
                                                        @else
                                                            {{ $voteSelectionItem->data }}
                                                        @endif
                                                    </td>
                                                    @if(Auth::check() && Auth::user()->isStaff() && !$voteEvent->isStarted())
                                                        <td class="text-right">
                                                            {!! link_to_route('vote-selection.edit', '編輯', $voteSelectionItem->id, ['class' => 'btn btn-default']) !!}
                                                            {!! Form::open(['route' => ['vote-selection.destroy', $voteSelectionItem->id], 'style' => 'display: inline', 'method' => 'DELETE',
                                                            'onSubmit' => "return confirm('確定要刪除此投票選項嗎？');"]) !!}
                                                            {!! Form::submit('刪除', ['class' => 'btn btn-danger']) !!}
                                                            {!! Form::close() !!}
                                                        </td>
                                                    @elseif($voteEvent->isEnded())
                                                        <td class="col-md-2">{{ $voteSelectionItem->getCount() }}</td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    @else
                                        <tr>
                                            <td>無投票選項</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
