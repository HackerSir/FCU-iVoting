@extends('app')

@section('title')
    {{ $showUser->getNickname() }} - 個人資料
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default">
                    <div class="panel-heading">{{ $showUser->getNickname() }} - 個人資料</div>
                    {{-- Panel body --}}
                    <div class="panel-body">
                        <div class="row">
                            <div class="text-center col-md-10 col-md-offset-1">
                                <table class="table table-hover">
                                    <tr>
                                        <td>Email：</td>
                                        <td>
                                            {{ $showUser->email }}
                                            @if($showUser->isConfirmed())
                                                <span class="label label-success">已驗證</span>
                                            @else
                                                <span class="label label-danger">未驗證</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>暱稱：</td>
                                        <td>{{ $showUser->nickname }}</td>
                                    </tr>
                                    <tr>
                                        <td>用戶組：</td>
                                        <td>
                                            @foreach($showUser->roles as $role)
                                                {{ $role->display_name }}<br />
                                            @endforeach
                                        </td>
                                    </tr>
                                    @if($user->isAdmin())
                                        <tr>
                                            <td colspan="2" class="danger">
                                                以下僅管理員可見
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>註解：</td>
                                            <td>{!! nl2br(htmlspecialchars($showUser->comment)) !!}</td>
                                        </tr>
                                        <tr>
                                            <td>註冊時間：</td>
                                            <td>{{ $showUser->register_at }}</td>
                                        </tr>
                                        <tr>
                                            <td>註冊IP：</td>
                                            <td>{{ $showUser->register_ip }}</td>
                                        </tr>
                                        <tr>
                                            <td>最後登入時間：</td>
                                            <td>{{ $showUser->lastlogin_at }}</td>
                                        </tr>
                                        <tr>
                                            <td>最後登入IP：</td>
                                            <td>{{ $showUser->lastlogin_ip }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">{!! HTML::linkRoute('member.edit-other-profile', '編輯資料', $showUser->id, ['class' => 'btn btn-primary']) !!}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
