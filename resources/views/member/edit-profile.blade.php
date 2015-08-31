@extends('app')

@section('title')
    編輯個人資料
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default">
                    <div class="panel-heading">編輯個人資料</div>
                    {{-- Panel body --}}
                    <div class="panel-body">
                        <div class="row">
                            {!! Form::open(['route' => 'member.edit-profile', 'class' => 'form-horizontal']) !!}
                                <div class="form-group has-feedback{{ ($errors->has('nickname'))?' has-error':'' }}">
                                    <label class="control-label col-md-2" for="nickname">暱稱</label>
                                    <div class="col-md-9">
                                        {!! Form::text('nickname', $user->nickname, ['id' => 'nickname', 'placeholder' => '請輸入暱稱', 'class' => 'form-control']) !!}
                                        @if($errors->has('nickname'))<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                                        <span class="label label-danger">{{ $errors->first('nickname') }}</span><br />@endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-9 col-md-offset-2">
                                        {!! Form::submit('修改資料', ['class' => 'btn btn-primary']) !!}
                                        {!! HTML::linkRoute('member.profile', '返回', null, ['class' => 'btn btn-default']) !!}
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
