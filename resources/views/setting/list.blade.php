@extends('app')

@section('title')
    網站設定
@endsection

@section('css')
    {!! HTML::style('css/no-more-table.css') !!}
    <style type="text/css">
        @media
        only screen and (max-width: 479px) {
            .container {
                padding:0;
                margin:0;
            }

            /*
            Label the data
            */
            .noMoreTable td:nth-of-type(1):before { content: "ID"; }
            .noMoreTable td:nth-of-type(2):before { content: "描述"; }
            .noMoreTable td:nth-of-type(3):before { content: "設定資料"; }
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-md-offset-0">
                <div class="panel panel-default">
                    <div class="panel-heading">網站設定</div>
                    {{-- Panel body --}}
                    <div class="panel-body">
                        <table class="table table-hover noMoreTable" style="margin-top: 5px">
                            <thead>
                            <tr>
                                <th class="col-md-2">ID</th>
                                <th class="col-md-5">描述</th>
                                <th class="col-md-5">設定資料</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($settingList as $settingItem)
                                <tr class="classData">
                                    <td>{!! HTML::linkRoute('setting.show', $settingItem->id, $settingItem->id, null) !!}</td>
                                    <td>{{ $settingItem->desc }}</td>
                                    <td>{{ $settingItem->data }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-12 col-md-offset-0">
                <div class="panel panel-default">
                    <div class="panel-heading">Mail測試</div>
                    <div class="panel-body">
                        {!! Form::open(['class' => 'form-inline', 'id' => 'sendTestMail']) !!}
                        <div class="form-group">
                            {!! Form::email('email', null, ['id' => 'testMailTo', 'placeholder' => 'Email', 'class' => 'form-control', 'required']) !!}
                        </div>
                        {!! Form::submit('寄送測試信', ['class' => 'btn btn-success', 'id' => 'btnSend']) !!}
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script type="text/javascript">
        $('#sendTestMail').submit(function (event) {
            event.preventDefault();

            $btnSend = $('#btnSend');
            $btnSend.prop('disabled', true);

            var URLs = "{{ URL::route('send-test-mail') }}";
            var val = $('#testMailTo').val();

            $.ajax({
                url: URLs,
                data: {email: val},
                headers: {
                    'X-CSRF-Token': "{{ Session::token() }}",
                    "Accept": "application/json"
                },
                type: "POST",
                dataType: "text",

                success: function (data) {
                    if (data == "success") {
                        globalNotify('成功寄出測試信');
                    }
                    else {
                        warningNotify('發生未知的錯誤', 0);
                    }

                    $btnSend.prop('disabled', false);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    warningNotify(xhr.status + ': ' + thrownError, 0);

                    $btnSend.prop('disabled', false);
                }
            });
        });

        function globalNotify(massage) {
            $.notify({
                // options
                message: massage
            },{
                // settings
                type: 'success',
                placement: {
                    align: 'center'
                },
                offset: 70,
                delay: 5000,
                timer: 500,
                mouse_over: 'pause',
                animate: {
                    enter: 'animated zoomIn',
                    exit: 'animated zoomOut'
                }
            });
        }

        function warningNotify(massage, delay) {
            $.notify({
                // options
                message: massage
            },{
                // settings
                type: 'danger',
                placement: {
                    align: 'center'
                },
                offset: 70,
                delay: delay,
                timer: 500,
                mouse_over: 'pause',
                animate: {
                    enter: 'animated rubberBand',
                    exit: 'animated zoomOut'
                }
            });
        }
    </script>
@endsection
