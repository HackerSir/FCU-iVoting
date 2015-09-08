@extends('app')

@section('title')
    信箱驗證
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">信箱驗證</div>
                    {{-- Panel body --}}
                    <div class="panel-body">
                        {!! Form::open(['route' => 'member.resend']) !!}
                            <div class="form-group">
                                <label class="control-label" for="email">信箱</label>
                                {!! Form::email('email', Auth::user()->email, ['id' => 'email', 'placeholder' => '信箱', 'class' => 'form-control', 'readonly']) !!}
                            </div>
                            <div><b>請注意：</b>
                                <ul>
                                    <li>
                                        請先確認您是此信箱擁有者，再點擊下方按鈕。<br />
                                        若此信箱不屬於您，請登出並重新以自己的信箱註冊帳號。
                                    </li>
                                    <li>
                                        驗證信件僅最後一封有效。
                                    </li>
                                    <li>
                                        發送郵件可能需等待數分鐘，請耐心等待，勿頻繁請求發送。
                                    </li>
                                </ul>
                            </div>
                            {!! Form::submit('重新發送驗證信', ['class' => 'btn btn-primary']) !!}
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
