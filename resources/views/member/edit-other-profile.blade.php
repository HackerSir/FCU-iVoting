@extends('app')

@section('title')
    編輯資料
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default">
                    <div class="panel-heading">編輯資料</div>
                    {{-- Panel body --}}
                    <div class="panel-body">
                        <div class="row">
                            {!! Form::open(['route' => ['member.edit-other-profile', $showUser->id], 'class' => 'form-horizontal']) !!}
                                <div class="form-group">
                                    <label class="control-label col-md-2" for="name">Email</label>
                                    <div class="col-md-9">
                                        {!! Form::email('email', $showUser->email, ['id' => 'email', 'placeholder' => '信箱', 'class' => 'form-control', 'readonly']) !!}
                                        <span class="label label-primary">信箱作為帳號使用，故無法修改</span>
                                    </div>
                                </div>
                            <div class="form-group has-feedback{{ ($errors->has('role'))?' has-error':'' }}">
                                <label class="control-label col-md-2" for="role">用戶組</label>
                                <div class="col-md-9">
                                    @foreach($roleList as $role)
                                        <div class="checkbox">
                                            <label>
                                                @if($showUser->id == Auth::user()->id && $role->name == 'admin')
                                                    {!! Form::checkbox('role[]', $role->id, $showUser->hasRole($role->name), ['disabled']) !!} {{ $role->display_name }}
                                                    <span class="label label-primary">禁止解除自己的管理員職務</span>
                                                @else
                                                    {!! Form::checkbox('role[]', $role->id, $showUser->hasRole($role->name)) !!} {{ $role->display_name }}
                                                @endif
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                                <div class="form-group">
                                    <div class="col-md-9 col-md-offset-2">
                                        {!! Form::submit('修改資料', ['class' => 'btn btn-primary']) !!}
                                        {!! HTML::linkRoute('member.profile', '返回', $showUser->id, ['class' => 'btn btn-default']) !!}
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
