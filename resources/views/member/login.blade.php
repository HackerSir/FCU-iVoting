@extends('app')

@section('title')
    登入
@endsection

@section('head-javascript')
    {!! HTML::script('https://www.google.com/recaptcha/api.js') !!}
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="well bs-component">
                {!! Form::open(['route' => 'member.login']) !!}
                    <fieldset>
                        <legend>登入</legend>
                    </fieldset>
                    <div class="form-group has-feedback{{ ($errors->has('email'))?' has-error':'' }}">
                        <label class="control-label" for="email">信箱
                            @if($errors->has('email'))
                                <span class="label label-danger">{{ $errors->first('email') }}</span>
                            @endif
                        </label>
                        {!! Form::email('email', null, ['id' => 'email', 'placeholder' => '請輸入信箱', 'class' => 'form-control', 'required']) !!}
                        @if($errors->has('email'))<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>@endif
                    </div>
                    <div class="form-group has-feedback{{ ($errors->has('password'))?' has-error':'' }}">
                        <label class="control-label" for="password">密碼 <a href="{{ URL::route('member.forgot-password') }}" tabindex="4">（忘記密碼）</a>
                            @if($errors->has('password'))
                                <span class="label label-danger">{{ $errors->first('password') }}</span>
                            @endif
                        </label>
                        {!! Form::password('password', ['id' => 'password', 'placeholder' => '請輸入密碼', 'class' => 'form-control', 'required']) !!}
                        @if($errors->has('password'))<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>@endif
                    </div>
                    @if(Throttle::get(URL::getRequest())->count() >= 3)
                        <div class="form-group has-feedback{{ ($errors->has('g-recaptcha-response'))?' has-error':'' }}">
                            <label class="control-label" for="password_again">驗證
                                @if($errors->has('g-recaptcha-response'))
                                    <span class="label label-danger">您必須通過驗證</span>
                                @endif
                            </label>

                            <div class="g-recaptcha" data-sitekey="{{ env('Data_Sitekey') }}"></div>
                        </div>
                    @endif
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('remember', 'remember', null, ['id' => 'remember']) !!} <strong>記住我</strong>
                            </label>
                        </div>
                    </div>
                    {!! Form::submit('登入', ['class' => 'btn btn-success']) !!}
                    <a href="{{ URL::route('member.register') }}" class="btn btn-default">註冊</a>
                {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
