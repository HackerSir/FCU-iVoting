@extends('app')

@section('title')
    網站設定
@endsection

@section('css')
    {!! HTML::style('css/no-more-table.css') !!}
    <style type="text/css">
        @media only screen and (max-width: 479px) {
            .container {
                padding: 0;
                margin: 0;
            }

            /*
            Label the data
            */
            .noMoreTable td {
                padding-left: inherit !important;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">網站設定</div>
            {{-- Panel body --}}
            <div class="panel-body">
                <table class="table table-hover noMoreTable" style="margin-top: 5px">
                    <thead>
                    <tr>
                        <th class="text-center col-md-5">設定項目</th>
                        <th class="text-center col-md-7">資料</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($settingList as $settingItem)
                        <tr class="classData">
                            <td class="col-md-5">
                                {!! HTML::linkRoute('setting.show', $settingItem->id, $settingItem->id, null) !!}
                                <span class="text-muted">（{{ $settingItem->getTypeDesc() }}）</span><br/>
                                <small><i class="fa fa-angle-double-right"></i> {{ $settingItem->desc }}</small>
                            </td>
                            <td>{!! $settingItem->getData() !!}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">Mail測試</div>
            <div class="panel-body">
                {!! Form::open(['class' => 'form-inline', 'id' => 'sendTestMail']) !!}
                <div class="form-group">
                    {!! Form::email('email', null, ['id' => 'testMailTo', 'placeholder' => 'Email', 'class' => 'form-control', 'required']) !!}
                </div>
                {!! Form::button('寄送測試信(使用Queue)', ['class' => 'btn btn-success', 'id' => 'btnSend_queue']) !!}
                {!! Form::button('寄送測試信', ['class' => 'btn btn-success', 'id' => 'btnSend']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script type="text/javascript">
        $btnSend_queue = $('#btnSend_queue');
        $btnSend = $('#btnSend');

        $btnSend_queue.click(function () {
            sendTestMailRequest('queue');
        });

        $btnSend.click(function () {
            sendTestMailRequest('normal');
        });

        function validateEmail(sEmail) {
            var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
            return filter.test(sEmail);
        }

        function sendTestMailRequest(type) {
            $btnSend_queue.prop('disabled', true);
            $btnSend.prop('disabled', true);

            var URLs = "{{ URL::route('send-test-mail') }}";
            var val = $('#testMailTo').val();
            val = $.trim(val);

            //輸入檢查
            if (val.length == 0) {
                notifyWarning('請輸入信箱');
                $btnSend_queue.prop('disabled', false);
                $btnSend.prop('disabled', false);
                return;
            }
            if (!validateEmail(val)) {
                notifyWarning('信箱格式錯誤');
                $btnSend_queue.prop('disabled', false);
                $btnSend.prop('disabled', false);
                return;
            }
            $.ajax({
                url: URLs,
                data: {email: val, type: type},
                headers: {
                    'X-CSRF-Token': "{{ Session::token() }}",
                    "Accept": "application/json"
                },
                type: "POST",
                dataType: "text",

                success: function (data) {
                    if (data == "success") {
                        notifySuccess('成功寄出測試信');
                    }
                    else {
                        notifyWarning('發生未知的錯誤');
                    }

                    $btnSend_queue.prop('disabled', false);
                    $btnSend.prop('disabled', false);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    notifyWarning(xhr.status + ': ' + thrownError);

                    $btnSend_queue.prop('disabled', false);
                    $btnSend.prop('disabled', false);
                }
            });
        }
    </script>
@endsection
