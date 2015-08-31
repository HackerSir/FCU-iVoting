@extends('app')

@section('title')
    成員清單
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-md-offset-0">
                <div class="panel panel-default">
                    <div class="panel-heading">成員清單</div>
                    {{-- Panel body --}}
                    <div class="panel-body">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>信箱</th>
                                <th>暱稱</th>
                                <th>註解</th>
                                <th>群組</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($userList as $userItem)
                                <tr>
                                    <td>
                                        <a href="{{ URL::route('member.profile',$userItem->id) }}">{{ $userItem->email }}</a>
                                        @if($userItem->isConfirmed())
                                            <span class="label label-success">已驗證</span>
                                        @else
                                            <span class="label label-danger">未驗證</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $userItem->nickname }}
                                    </td>
                                    <td>
                                        {!! nl2br(htmlspecialchars($userItem->comment)) !!}
                                    </td>
                                    <td>
                                        @foreach($userItem->roles as $role)
                                            {{ $role->display_name }}<br />
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="text-center">
                            {!! str_replace('/?', '?', $userList->render()) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
