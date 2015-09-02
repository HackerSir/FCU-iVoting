@extends('app')

@section('title')
    統計
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-md-offset-0">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        統計（更新時間：<span title="更新時間：{{ (new Carbon($stats->time))->diffForHumans() }}<br />下次更新：{{ (new Carbon($stats->time))->addMinutes($cacheMinute)->diffForHumans() }}">{{ $stats->time }}</span>）
                        {!! link_to_route('stats.force-renew','強制更新',null,['class' => 'btn btn-xs btn-primary', 'title' => '強制更新緩存資料']) !!}
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="col-md-4">項目</th>
                                    <th class="col-md-8">資料</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats->data as $key => $data)
                                    <tr>
                                        <td>{{ $key }}</td>
                                        <td>{{ $data }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
