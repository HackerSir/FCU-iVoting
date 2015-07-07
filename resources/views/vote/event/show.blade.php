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
                                        <td>最多可選幾項：</td>
                                        <td>{{ $voteEvent->getMaxSelected() }}
                                            @if(Auth::check() && Auth::user()->isStaff())
                                                <br />（設定值：{{ $voteEvent->max_selected }}）
                                            @endif
                                        </td>
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
                                    @else
                                        {!! HTML::linkRoute('vote-event.index', '返回投票活動列表', [], ['class' => 'btn btn-default']) !!}
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
                                                        @if(Auth::check() && $voteSelectionItem->hasVoted(Auth::user()))
                                                            <span title="我的選擇">✔</span>
                                                        @endif
                                                        {!! HTML::linkRoute('vote-selection.show', $voteSelectionItem->getTitle(),$voteSelectionItem->id, null) !!}
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
