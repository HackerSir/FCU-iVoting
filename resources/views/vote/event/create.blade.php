@extends('app')

@section('title')
    新增投票活動
@endsection

@section('content')
    <div class="container" style="min-height: 600px">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default">
                    <div class="panel-heading">新增投票活動</div>
                    {{-- Panel body --}}
                    <div class="panel-body">
                        <div class="row">
                            {!! Form::open(['route' => 'vote-event.store', 'class' => 'form-horizontal']) !!}
                                <div class="form-group has-feedback{{ ($errors->has('subject'))?' has-error':'' }}">
                                    <label class="control-label col-md-2" for="subject">投票主題</label>
                                    <div class="col-md-9">
                                        {!! Form::text('subject', null, ['id' => 'subject', 'placeholder' => '請輸入投票主題', 'class' => 'form-control', 'required']) !!}
                                        @if($errors->has('subject'))<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                                        <span class="label label-danger">{{ $errors->first('subject') }}</span>@endif
                                    </div>
                                </div>
                                <div class="form-group has-feedback{{ ($errors->has('open_time'))?' has-error':'' }}">
                                    <label class="control-label col-md-2" for="open_time">開始時間</label>
                                    <div class="col-md-9">
                                        <div class='input-group date' id='datetimepicker1'>
                                            {!! Form::text('open_time', null, ['id' => 'open_time', 'placeholder' => 'YYYY/MM/DD HH:mm:ss', 'class' => 'form-control']) !!}
                                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                        </div>
                                        @if($errors->has('open_time'))<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                                        <span class="label label-danger">{{ $errors->first('open_time') }}</span>@endif
                                    </div>
                                </div>
                                <div class="form-group has-feedback{{ ($errors->has('close_time'))?' has-error':'' }}">
                                    <label class="control-label col-md-2" for="close_time">結束時間</label>
                                    <div class="col-md-9">
                                        <div class='input-group date' id='datetimepicker2'>
                                            {!! Form::text('close_time', null, ['id' => 'close_time', 'placeholder' => 'YYYY/MM/DD HH:mm:ss', 'class' => 'form-control']) !!}
                                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                        </div>
                                        @if($errors->has('close_time'))<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                                        <span class="label label-danger">{{ $errors->first('close_time') }}</span>@endif
                                    </div>
                                </div>
                                <div class="form-group has-feedback{{ ($errors->has('max_selected'))?' has-error':'' }}">
                                    <label class="control-label col-md-2" for="max_selected">最大數量</label>
                                    <div class="col-md-9">
                                        {!! Form::number('max_selected', null, ['id' => 'max_selected', 'placeholder' => '每人最多可選擇之數量，預設為1', 'class' => 'form-control', 'min' => 1]) !!}
                                        @if($errors->has('max_selected'))<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                                        <span class="label label-danger">{{ $errors->first('max_selected') }}</span>@endif
                                    </div>
                                </div>
                                <div class="form-group has-feedback{{ ($errors->has('info'))?' has-error':'' }}">
                                    <label class="control-label col-md-2" for="info">內容簡介</label>
                                    <div class="col-md-9" role="tabpanel">
                                        <ul class="nav nav-tabs" role="tablist">
                                            <li role="presentation" class="active"><a href="#edit" aria-controls="edit" role="tab" data-toggle="tab" id="tab_edit">編輯</a></li>
                                            <li role="presentation"><a href="#preview" aria-controls="preview" role="tab" data-toggle="tab" id="tab_preview">預覽</a></li>
                                        </ul>
                                    </div>
                                    <div class="col-md-12">
                                        <!-- Tab panes -->
                                        <div class="tab-content">
                                            <div role="tabpanel" class="tab-pane active" id="edit">
                                                {!! Form::textarea('info', null, ['id' => 'info', 'placeholder' => '請輸入內容簡介', 'class' => 'form-control']) !!}
                                            </div>
                                            <div role="tabpanel" class="tab-pane" id="preview">
                                                Loading...
                                            </div>
                                        </div>
                                        @if($errors->has('info'))<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                                        <span class="label label-danger">{{ $errors->first('info') }}</span>@endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-9 col-md-offset-2">
                                        {!! Form::submit('新增投票活動', ['class' => 'btn btn-primary']) !!}
                                        {!! HTML::linkRoute('vote-event.index', '返回', [], ['class' => 'btn btn-default']) !!}
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

@section('javascript')
    $(function () {
        $('#datetimepicker1').datetimepicker({
            format: 'YYYY/MM/DD HH:mm:ss'
        });
        $('#datetimepicker2').datetimepicker({
            format: 'YYYY/MM/DD HH:mm:ss'
        });
    });

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        // e.target -> newly activated tab
        if(e.target.id == 'tab_preview'){
            $("#preview").html("Loading...");

            var URLs = "{{ URL::route('markdown.preview') }}"
            var val = $('#edit textarea').val();

            $.ajax({
                url: URLs,
                data: val,
                headers: {
                    'X-CSRF-Token': "{{ Session::token() }}" ,
                    "Accept": "application/json"
                },
                type:"POST",
                dataType: "text",

                success: function(data){
                    if(data){
                        $("#preview").html(data);
                    }else{
                        alert("error");
                    }
                },
                error: function(xhr, ajaxOptions, thrownError){
                    alert(xhr.status);
                    alert(thrownError);
                }
            });
        }
    })
@endsection

