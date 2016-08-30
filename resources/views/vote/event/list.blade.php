@extends('app')

@inject('voteEventPresenter', 'Hackersir\Presenters\VoteEventPresenter')

@section('title')
    投票活動清單
@endsection

@section('css')
    <style type="text/css">
        .label {
            position: relative;
            top: -5px;
        }
    </style>
@endsection

@section('content')
    <div class="container container-background">
        <h1>
            <i class="fa fa-list" aria-hidden="true" style="position: relative; top: 3px;"></i>
            票選活動
            @if(Auth::check() && Auth::user()->isStaff())
                <a href="{{ route('voteEvent.create') }}" class="btn btn-primary pull-right">
                    <i class="fa fa-plus-circle fa-lg" aria-hidden="true"></i>&nbsp; 新增投票活動
                </a>
            @endif
        </h1>
        <div class="clearfix"></div>
        <ul class="list-group" style="margin-top: 2px;">
            @foreach($voteEventList as $voteEvent)
                <li class="list-group-item @if(Auth::check() && Auth::user()->isStaff() && !$voteEvent->isVisible()) list-group-item-danger @endif"
                    style="padding: 15px;">
                    <h2 style="margin: 0; min-height: 36px;">
                        <a href="{{ route('voteEvent.show', [$voteEvent->id]) }}"
                           style="position: relative; top: -2px;">
                            {{ $voteEvent->subject }}
                        </a>
                        {!! $voteEventPresenter->getStatusLabel($voteEvent) !!}
                        @if (Auth::check() && Auth::user()->isStaff())
                            @if(!$voteEvent->show)
                                <span class="glyphicon glyphicon-eye-close" aria-hidden="true"
                                      title="活動開始前是不顯示的"></span>
                            @endif
                            @if(!$voteEvent->isEnded())
                                <a href="{{ URL::route('voteEvent.edit', $voteEvent->id) }}"
                                   title="編輯投票活動"><span class="glyphicon glyphicon-cog"
                                                        aria-hidden="true"></span></a>
                            @endif
                        @endif
                    </h2>
                    <p style="margin: 0; font-size: 150%;">
                        {!! $voteEventPresenter->getHumanTimeString($voteEvent) !!}
                    </p>
                </li>
            @endforeach
        </ul>
        <div class="text-center">
            {!! str_replace('/?', '?', $voteEventList->appends(Input::except(array('page')))->render()) !!}
        </div>
    </div>
@endsection
