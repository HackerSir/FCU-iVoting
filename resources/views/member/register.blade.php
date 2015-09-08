@extends('app')

@section('title')
    註冊
@endsection

@section('head-javascript')
    {!! HTML::script('https://www.google.com/recaptcha/api.js') !!}
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="well bs-component">
                    {!! Form::open(['route' => 'member.register']) !!}
                    <fieldset>
                        <legend>註冊</legend>
                    </fieldset>
                    <div class="form-group has-feedback{{ ($errors->has('email_name'))?' has-error':'' }}">
                        <label class="control-label" for="email_name">信箱（註冊後請收取郵件並完成驗證）
                            @if($errors->has('email_name'))
                                <span class="label label-danger">{{ $errors->first('email_name') }}</span>
                            @endif
                        </label>

                        <div class="input-group">
                            {!! Form::text('email_name', null, ['id' => 'email_name', 'placeholder' => '學號, EX: d0000000', 'class' => 'form-control', 'required']) !!}
                            <div class="input-group-addon">@</div>
                            {!! Form::select('email_domain', $allowedEmailsArray, null, ['class' => 'form-control', 'required']) !!}
                        </div>
                        @if($errors->has('email_name'))<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>@endif
                    </div>
                    <div class="form-group has-feedback{{ ($errors->has('password'))?' has-error':'' }}">
                        <label class="control-label" for="password">密碼
                            @if($errors->has('password'))
                                <span class="label label-danger">{{ $errors->first('password') }}</span>
                            @endif
                        </label>
                        {!! Form::password('password', ['id' => 'password', 'placeholder' => '請輸入密碼', 'class' => 'form-control', 'required']) !!}
                        @if($errors->has('password'))<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>@endif
                    </div>
                    <div class="form-group has-feedback{{ ($errors->has('password_again'))?' has-error':'' }}">
                        <label class="control-label" for="password_again">密碼（再輸入一次）
                            @if($errors->has('password_again'))
                                <span class="label label-danger">{{ $errors->first('password_again') }}</span>
                            @endif
                        </label>
                        {!! Form::password('password_again', ['id' => 'password_again', 'placeholder' => '請再輸入一次密碼', 'class' => 'form-control', 'required']) !!}
                        @if($errors->has('password_again'))<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>@endif
                    </div>
                    <div class="form-group has-feedback{{ ($errors->has('g-recaptcha-response'))?' has-error':'' }}">
                        <label class="control-label" for="password_again">驗證
                            @if($errors->has('g-recaptcha-response'))
                                <span class="label label-danger">您必須勾選「我不是機器人」</span>
                            @endif
                        </label>
                        @if(App::environment('production'))
                            <div class="g-recaptcha" data-sitekey="{{ env('Data_Sitekey') }}"></div>
                        @else
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('g-recaptcha-response', true, null,['class' => 'checkbox']) !!} <strong>我不是機器人</strong>
                                </label>
                            </div>
                        @endif
                    </div>
                    <p class="help-block">當你點選註冊時，代表你同意本站的<a href="{{ URL::route('policies', 'privacy') }}" target="_blank">《隱私權政策》<span class="glyphicon glyphicon-new-window" aria-hidden="true"></span></a>&nbsp;與<a href="{{ URL::route('policies', 'terms') }}" target="_blank">《服務條款》<span class="glyphicon glyphicon-new-window" aria-hidden="true"></span></a></p>
                    {!! Form::submit('註冊', ['class' => 'btn btn-primary']) !!}
                    <a href="{{ URL::route('member.login') }}", class="btn btn-default">返回登入頁</a>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function () {
            {{-- 將 「--請下拉選擇--」 設定成不可選 --}}
            $("select[name='email_domain'] option[value='']").prop('disabled', true);
        });
    </script>
@endsection
