@extends('app')

@section('title')
    修改設定
@endsection

@section('content')
    <div class="container" style="min-height: 600px">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">修改設定</div>
                    {{-- Panel body --}}
                    <div class="panel-body">
                        <div class="row">
                            {!! Form::open(['route' => ['setting.update', $setting->id], 'class' => 'form-horizontal', 'method' => 'PUT']) !!}
                                <div class="form-group">
                                    <label class="control-label col-md-2" for="subject">ID</label>
                                    <div class="col-md-9">
                                        {!! HTML::linkRoute('setting.show', $setting->id, $setting->id, null) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2" for="subject">描述</label>
                                    <div class="col-md-9">
                                        {{ $setting->desc }}
                                    </div>
                                </div>
                                <div class="form-group has-feedback{{ ($errors->has('data'))?' has-error':'' }}">
                                    <label class="control-label col-md-2" for="data">設定資料</label>
                                    <div class="col-md-9">
                                        {!! Form::textarea('data',  $setting->data, ['id' => 'data', 'placeholder' => '請輸入設定資料', 'class' => 'form-control', 'rows' => 5]) !!}
                                        @if($errors->has('data'))<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                                        <span class="label label-danger">{{ $errors->first('data') }}</span>@endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-10 col-md-offset-1 text-center">
                                        <hr />
                                        {!! Form::submit('修改資料', ['class' => 'btn btn-primary']) !!}
                                        {!! HTML::linkRoute('setting.show', '返回', $setting->id, ['class' => 'btn btn-default']) !!}
                                    </div>
                                </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection