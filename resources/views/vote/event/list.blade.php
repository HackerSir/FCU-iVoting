@extends('app')

@section('title')
    投票活動清單
@endsection

@section('head')
    {!! HTML::style('css/no-more-table.css'); !!}
    <style type="text/css">
        @media
        only screen and (max-width: 479px) {
            .container {
                padding:0;
                margin:0;
            }

            /*
            Label the data
            */
            .noMoreTable td:nth-of-type(1):before { content: "狀態"; }
            .noMoreTable td:nth-of-type(2):before { content: "投票主題"; }
            .noMoreTable td:nth-of-type(3):before { content: "開始時間"; }
            .noMoreTable td:nth-of-type(4):before { content: "結束時間"; }
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-md-offset-0">
                <div class="panel panel-default">
                    <div class="panel-heading">投票活動清單</div>
                    {{-- Panel body --}}
                    <div class="panel-body">
                        @if(Auth::check() && Auth::user()->isStaff())
                            {!! HTML::linkRoute('vote-event.create', '新增投票活動', [], ['class' => 'btn btn-primary pull-right']) !!}
                        @endif
                        <table class="table table-hover noMoreTable" style="margin-top: 5px">
                            <thead>
                            <tr>
                                <th class="col-md-1">狀態</th>
                                <th class="col-md-5">投票主題</th>
                                <th class="col-md-3">開始時間</th>
                                <th class="col-md-3">結束時間</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($voteEventList as $voteEventItem)
                                <tr class="classData">
                                    <td>
                                        @if($voteEventItem->isEnded())
                                            已結束
                                        @elseif($voteEventItem->isInProgress())
                                            進行中
                                        @else
                                            未開始
                                        @endif
                                    </td>
                                    <td>{!! HTML::linkRoute('vote-event.show', $voteEventItem->subject, $voteEventItem->id, null) !!}</td>
                                    <td>{{ $voteEventItem->open_time }}</td>
                                    <td>{{ $voteEventItem->close_time }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="text-center">
                            {!! str_replace('/?', '?', $voteEventList->appends(Input::except(array('page')))->render()); !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection