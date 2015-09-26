@extends('app')

@section('title')
    {{ $setting->id }} - 網站設定
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">{{ $setting->id }} - 網站設定</div>
                    {{-- Panel body --}}
                    <div class="panel-body">
                        <div class="row">
                            <div class="text-center col-md-12 col-md-offset-0">
                                <table class="table table-hover">
                                    <tr>
                                        <td class="col-md-2">ID：</td>
                                        <td>{{ $setting->id }}</td>
                                    </tr>
                                    <tr>
                                        <td>類型：</td>
                                        <td>{{ $setting->getTypeDesc() }}</td>
                                    </tr>
                                    <tr>
                                        <td>描述：</td>
                                        <td>{{ $setting->desc }}</td>
                                    </tr>
                                    <tr>
                                        <td>設定資料：</td>
                                        <td>{!! $setting->getData() !!}</td>
                                    </tr>
                                </table>
                                <div>
                                    {!! HTML::linkRoute('setting.edit', '修改設定', $setting->id, ['class' => 'btn btn-primary']) !!}
                                    {!! HTML::linkRoute('setting.index', '返回網站設定', [], ['class' => 'btn btn-default']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
