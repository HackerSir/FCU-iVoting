@extends('app')

@section('title')
    註冊
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
                        <label class="control-label" for="email_name">信箱
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
                    {!! Form::submit('註冊', ['class' => 'btn btn-primary']) !!}
                    <a href="{{ URL::route('member.login') }}", class="btn btn-default">返回登入頁</a>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
