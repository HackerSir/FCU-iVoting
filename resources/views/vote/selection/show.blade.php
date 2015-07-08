@extends('app')

@section('title')
    {{ $voteSelection->getTitle() }} - 投票選項
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">{{ $voteSelection->getTitle() }} - 投票選項</div>
                    {{-- Panel body --}}
                    <div class="panel-body">
                        <div class="row">
                            <div class="text-center col-md-12 col-md-offset-0">
                                <table class="table table-hover">
                                    <tr>
                                        <td class="col-md-2">投票活動：</td>
                                        <td>
                                            {!! HTML::linkRoute('vote-event.show', $voteSelection->voteEvent->subject, $voteSelection->voteEvent->id, null) !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-2">投票選項：</td>
                                        <td>{{ $voteSelection->getTitle() }}</td>
                                    </tr>
                                    @if(Auth::check() && Auth::user()->isStaff())
                                    <tr>
                                        <td class="col-md-2">原始資料：</td>
                                        <td>
                                            <pre class="text-left">{{ json_encode(json_decode($voteSelection->data), JSON_PRETTY_PRINT) }}</pre>
                                        </td>
                                    </tr>
                                    @endif
                                </table>
                                <hr />
                                <div>
                                    @if(!Auth::check())
                                        <div title="登入以完成投票" style="display: inline-block">
                                            <span class="btn btn-default btn-lg" disabled>按此投票</span>
                                        </div>
                                    @elseif(!Auth::user()->isConfirmed())
                                        {!! HTML::linkRoute('member.resend', '按此投票', [], ['title' => '投票前請先完成信箱驗證', 'class' => 'btn btn-default btn-lg']) !!}
                                    @else
                                        @if($voteSelection->voteEvent->isInProgress())
                                            此活動最多可選{{ $voteSelection->voteEvent->getMaxSelected() }}項，您已選擇{{ $voteSelection->voteEvent->getSelected(Auth::user()) }}項<br />
                                            @if($voteSelection->voteEvent->getMaxSelected() > $voteSelection->voteEvent->getSelected(Auth::user()))
                                                @if(!$voteSelection->hasVoted(Auth::user()))
                                                    {!! Form::open(['route' => ['vote-selection.vote', $voteSelection->id], 'style' => 'display: inline', 'method' => 'POST',
                                                    'onSubmit' => "return confirm('確定要投票給此項目嗎？');"]) !!}
                                                    {!! Form::submit('按此投票', ['class' => 'btn btn-primary btn-lg']) !!}
                                                    {!! Form::close() !!}
                                                @else($voteSelection->hasVoted(Auth::user()))
                                                    <div title="您已經投過此項目" style="display: inline-block">
                                                        <span class="btn btn-default btn-lg" disabled>按此投票</span>
                                                    </div>
                                                @endif
                                            @else
                                                <div title="您已經完成投票" style="display: inline-block">
                                                    <span class="btn btn-default btn-lg" disabled>按此投票</span>
                                                </div>
                                            @endif
                                        @else
                                            <div title="非投票期間" style="display: inline-block">
                                                <span class="btn btn-default btn-lg" disabled>按此投票</span>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                                <div>
                                    <br />
                                    @if(Auth::check() && Auth::user()->isStaff() && !$voteSelection->voteEvent->isStarted())
                                        {!! link_to_route('vote-selection.edit', '編輯', $voteSelection->id, ['class' => 'btn btn-default']) !!}
                                        {!! Form::open(['route' => ['vote-selection.destroy', $voteSelection->id], 'style' => 'display: inline', 'method' => 'DELETE',
                                        'onSubmit' => "return confirm('確定要刪除此投票選項嗎？');"]) !!}
                                        {!! Form::submit('刪除', ['class' => 'btn btn-danger']) !!}
                                        {!! Form::close() !!}
                                    @endif
                                    {!! HTML::linkRoute('vote-event.show', '返回投票活動', $voteSelection->voteEvent->id, ['class' => 'btn btn-default']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
