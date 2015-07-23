@extends('app')

@section('title')
    {{ $organizer->name }} - 主辦單位
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">{{ $organizer->name }} - 主辦單位</div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="text-center col-md-12 col-md-offset-0">
                                <table class="table table-hover">
                                    <tr>
                                        <td class="col-md-2">主辦單位名稱：</td>
                                        <td>{{ $organizer->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-2">網址：</td>
                                        <td>
                                            @if(!empty($organizer->url))
                                                {!! link_to($organizer->url, null, ['target' => '_blank']) !!}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-2">LOGO網址：</td>
                                        <td>
                                            @if(!empty($organizer->logo_url))
                                                {!! link_to($organizer->logo_url, null, ['target' => '_blank']) !!}
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                                <div>
                                    {!! HTML::linkRoute('organizer.edit', '編輯主辦單位', $organizer->id, ['class' => 'btn btn-primary']) !!}
                                    {!! HTML::linkRoute('organizer.index', '返回主辦單位列表', [], ['class' => 'btn btn-default']) !!}
                                    {!! Form::open(['route' => ['organizer.destroy', $organizer->id], 'style' => 'display: inline', 'method' => 'DELETE',
                                    'onSubmit' => "return confirm('確定要刪除主辦單位嗎？');"]) !!}
                                    {!! Form::submit('刪除', ['class' => 'btn btn-danger']) !!}
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
