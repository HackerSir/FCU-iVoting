@extends('app')

@section('title')
    新增主辦單位
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="well bs-component">
                    {!! Form::open(['route' => 'organizer.store', 'class' => 'form-horizontal']) !!}
                    <fieldset>
                        <legend>新增主辦單位</legend>
                        <div class="form-group has-feedback{{ ($errors->has('name'))?' has-error':'' }}">
                            <label class="control-label col-md-2" for="name">主辦單位名稱</label>

                            <div class="col-md-9">
                                {!! Form::text('name', null, ['id' => 'name', 'placeholder' => '請輸入主辦單位名稱', 'class' => 'form-control', 'required']) !!}
                                @if($errors->has('name'))<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                                <span class="label label-danger">{{ $errors->first('name') }}</span>@endif
                            </div>
                        </div>
                        <div class="form-group has-feedback{{ ($errors->has('url'))?' has-error':'' }}">
                            <label class="control-label col-md-2" for="url">網址</label>

                            <div class="col-md-9">
                                {!! Form::url('url', null, ['id' => 'url', 'placeholder' => '請輸入主辦單位網址', 'class' => 'form-control']) !!}
                                @if($errors->has('url'))<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                                <span class="label label-danger">{{ $errors->first('url') }}</span>@endif
                            </div>
                        </div>
                        <div class="form-group has-feedback{{ ($errors->has('logo_url'))?' has-error':'' }}">
                            <label class="control-label col-md-2" for="logo_url">LOGO網址</label>

                            <div class="col-md-9">
                                {!! Form::url('logo_url', null, ['id' => 'logo_url', 'placeholder' => '請輸入主辦單位LOGO網址', 'class' => 'form-control']) !!}
                                @if($errors->has('logo_url'))<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                                <span class="label label-danger">{{ $errors->first('logo_url') }}</span>@endif
                            </div>
                        </div>
                        <hr />
                        <div class="form-group">
                            <div class="col-md-9 col-md-offset-2">
                                {!! Form::submit('新增主辦單位', ['class' => 'btn btn-primary']) !!}
                                {!! HTML::linkRoute('organizer.index', '返回', [], ['class' => 'btn btn-default']) !!}
                            </div>
                        </div>
                    </fieldset>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
