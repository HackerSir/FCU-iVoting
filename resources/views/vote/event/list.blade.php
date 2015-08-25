@extends('app')

@section('title')
    投票活動清單
@endsection

@section('css')
    {!! HTML::style('css/no-more-table.css') !!}
    <style type="text/css">
        @media
        only screen and (max-width: 479px) {
            /*
            Label the data
            */
            .noMoreTable td:nth-of-type(1):before { content: "狀態"; }
            .noMoreTable td:nth-of-type(2):before { content: "投票主題"; }
            .noMoreTable td:nth-of-type(4):before { content: "開始時間"; }
            .noMoreTable td:nth-of-type(5):before { content: "結束時間"; }
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-md-offset-0">
                <div class="panel panel-default">
                    <div class="panel-heading">票選活動清單</div>
                    {{-- Panel body --}}
                    <div class="panel-body">
                        @if(Auth::check() && Auth::user()->isStaff())
                            {!! HTML::linkRoute('vote-event.create', '新增投票活動', [], ['class' => 'btn btn-primary pull-right']) !!}
                            <div class="clearfix"></div>
                        @endif
                        <table class="table table-hover noMoreTable" style="margin-top: 5px">
                            <thead>
                            <tr>
                                <th class="col-md-1">狀態</th>
                                <th class="col-md-4">投票主題</th>
                                <th></th>
                                <th class="col-md-offset-1 col-md-3">開始時間</th>
                                <th class="col-md-3">結束時間</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($voteEventList as $voteEventItem)
                                @if(Auth::check() && Auth::user()->isStaff() && !$voteEventItem->isVisible())
                                    <tr class="classData danger">
                                @else
                                    <tr class="classData">
                                @endif
                                    <td>
                                        @if($voteEventItem->isEnded())
                                            <span class="label label-warning">已結束</span>
                                        @elseif($voteEventItem->isInProgress())
                                            <span class="label label-success">進行中</span>
                                        @else
                                            <span class="label label-default">未開始</span>
                                        @endif
                                    </td>
                                    <td>{!! HTML::linkRoute('vote-event.show', $voteEventItem->subject, $voteEventItem->id, null) !!}</td>
                                    <td class="hidePhone">
                                        <div class="pull-right">
                                            @if (Auth::check() && Auth::user()->isStaff())
                                                @if(!$voteEventItem->show)
                                                    <span class="glyphicon glyphicon-eye-close" aria-hidden="true" title="活動開始前是不顯示的"></span>
                                                @endif
                                                @if(!$voteEventItem->isEnded())
                                                    <a href="{{ URL::route('vote-event.edit', $voteEventItem->id) }}" title="編輯投票活動"><span class="glyphicon glyphicon-cog" aria-hidden="true" ></span></a>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                    <td><span title="{{ (new Carbon($voteEventItem->open_time))->diffForHumans() }}">{{ $voteEventItem->open_time }}</span></td>
                                    <td><span title="{{ (new Carbon($voteEventItem->close_time))->diffForHumans() }}">{{ $voteEventItem->close_time }}</span></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="text-center">
                            {!! str_replace('/?', '?', $voteEventList->appends(Input::except(array('page')))->render()) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
