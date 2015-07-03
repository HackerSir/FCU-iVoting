@extends('app')

@section('title')
    網站設定
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
            .noMoreTable td:nth-of-type(1):before { content: "ID"; }
            .noMoreTable td:nth-of-type(2):before { content: "描述"; }
            .noMoreTable td:nth-of-type(3):before { content: "設定資料"; }
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-md-offset-0">
                <div class="panel panel-default">
                    <div class="panel-heading">網站設定</div>
                    {{-- Panel body --}}
                    <div class="panel-body">
                        <table class="table table-hover noMoreTable" style="margin-top: 5px">
                            <thead>
                            <tr>
                                <th class="col-md-2">ID</th>
                                <th class="col-md-5">描述</th>
                                <th class="col-md-5">設定資料</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($settingList as $settingItem)
                                <tr class="classData">
                                    <td>{!! HTML::linkRoute('setting.show', $settingItem->id, $settingItem->id, null) !!}</td>
                                    <td>{{ $settingItem->desc }}</td>
                                    <td>{{ $settingItem->data }}</td>
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
