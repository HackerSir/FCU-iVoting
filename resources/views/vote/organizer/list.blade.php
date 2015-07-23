@extends('app')

@section('title')
    主辦單位清單
@endsection

@section('content')
    <div class="container container-mobile">
        <div class="row">
            <div class="col-md-12 col-md-offset-0">
                <div class="panel panel-default">
                    <div class="panel-heading">主辦單位清單</div>
                    <div class="panel-body">
                        {!! HTML::linkRoute('organizer.create', '新增主辦單位', [], ['class' => 'btn btn-primary pull-right']) !!}
                        <div class="clearfix"></div>
                        <table class="table table-hover noMoreTable" style="margin-top: 5px">
                            <thead>
                            <tr>
                                <th class="col-md-1"></th>
                                <th class="col-md-4">主辦單位名稱</th>
                                <th class="col-md-1"></th>
                                <th class="col-md-6">網址</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($organizerList as $organizer)
                                <tr class="classData">
                                    <td>{{ $organizer->id }}</td>
                                    <td>{!! HTML::linkRoute('organizer.show', $organizer->name, $organizer->id, null) !!}</td>
                                    <td class="hidePhone">
                                        <a href="{{ URL::route('organizer.edit', $organizer->id) }}" class="pull-right" title="編輯主辦單位"><span class="glyphicon glyphicon-cog" aria-hidden="true" /></a>
                                        @if(count($organizer->voteEvents))
                                            <span class="badge pull-right" title="共舉辦 {{ count($organizer->voteEvents) }} 場活動">{{ count($organizer->voteEvents) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($organizer->url))
                                            {!! link_to($organizer->url, null, ['target' => '_blank']) !!}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="text-center">
                            {!! str_replace('/?', '?', $organizerList->appends(Input::except(array('page')))->render()) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
