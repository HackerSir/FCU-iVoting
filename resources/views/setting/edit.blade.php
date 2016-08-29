@extends('app')

@section('title')
    修改設定
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="well bs-component">
                    {!! Form::open(['route' => ['setting.update', $setting->id], 'class' => 'form-horizontal', 'method' => 'PUT']) !!}
                    <fieldset>
                        <legend>修改設定</legend>
                    </fieldset>
                    <div class="form-group">
                        <label class="control-label col-md-2" for="subject">ID</label>

                        <div class="col-md-9 form-control-static">
                            {!! HTML::linkRoute('setting.show', $setting->id, $setting->id, null) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2" for="subject">類型</label>

                        <div class="col-md-9 form-control-static">
                            {{ $setting->getTypeDesc() }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2" for="subject">描述</label>

                        <div class="col-md-9 form-control-static">
                            {{ $setting->desc }}
                        </div>
                    </div>
                    <div class="form-group has-feedback{{ ($errors->has('data'))?' has-error':'' }}">
                        <label class="control-label col-md-2" for="data">設定資料</label>

                        @if($setting->getType()=='text')
                            <div class="col-md-9">
                                {!! Form::text('data',  $setting->data, ['id' => 'data', 'placeholder' => '請輸入設定資料', 'class' => 'form-control']) !!}
                                @if($errors->has('data'))<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                                <span class="label label-danger">{{ $errors->first('data') }}</span>@endif
                            </div>
                        @elseif($setting->getType()=='multiline')
                            <div class="col-md-9">
                                {!! Form::textarea('data',  $setting->data, ['id' => 'data', 'placeholder' => '請輸入設定資料', 'class' => 'form-control', 'style' => 'resize: vertical;', 'rows' => 5]) !!}
                                @if($errors->has('data'))<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                                <span class="label label-danger">{{ $errors->first('data') }}</span>@endif
                            </div>
                        @elseif($setting->getType()=='markdown')
                            <div class="col-md-9" role="tabpanel">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active"><a href="#edit" aria-controls="edit" role="tab" data-toggle="tab" id="tab_edit">編輯</a></li>
                                    <li role="presentation"><a href="#preview" aria-controls="preview" role="tab" data-toggle="tab" id="tab_preview">預覽</a></li>
                                </ul>
                            </div>
                            <div class="col-md-9 col-md-offset-2">
                                <!-- Tab panes -->
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane active" id="edit">
                                        {!! Form::textarea('data', $setting->data, [
                                            'id' => 'data',
                                            'placeholder' => '請輸入設定資料',
                                            'class' => 'form-control',
                                            'style' => 'resize: vertical; font-family: Consolas, monospace;'
                                        ]) !!}
                                        <small>
                                            <b>提示：</b>設定資料支援{!! link_to('http://markdown.tw/', 'Markdown', ['target' => '_blank']) !!}語法
                                        </small>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="preview" style="background-color: white; border: 1px solid #cccccc; padding: 8px 12px;">
                                    </div>
                                </div>
                                @if($errors->has('data'))<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                                <span class="label label-danger">{{ $errors->first('data') }}</span>@endif
                            </div>
                        @elseif($setting->getType()=='url')
                            <div class="col-md-9">
                                {!! Form::url('data',  $setting->data, ['id' => 'data', 'placeholder' => '請輸入設定資料', 'class' => 'form-control']) !!}
                                @if($errors->has('data'))<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                                <span class="label label-danger">{{ $errors->first('data') }}</span>@endif
                            </div>
                        @endif

                    </div>
                    <div class="form-group">
                        <div class="col-md-10 col-md-offset-1 text-center">
                            <hr/>
                            {!! Form::submit('修改資料', ['class' => 'btn btn-primary']) !!}
                            {!! HTML::linkRoute('setting.show', '返回', $setting->id, ['class' => 'btn btn-default']) !!}
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script type="text/javascript">
        @if($setting->getType()=='markdown')
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                // e.target -> newly activated tab
                if (e.target.id == 'tab_preview') {
                    $("#preview").html("");

                    var URLs = "{{ URL::route('markdown.preview') }}";
                    var val = $('#data').val();

                    $.ajax({
                        url: URLs,
                        data: val,
                        headers: {
                            'X-CSRF-Token': "{{ Session::token() }}",
                            "Accept": "application/json"
                        },
                        type: "POST",
                        dataType: "text",

                        success: function (data) {
                            if (data) {
                                $("#preview").html((data != " ") ? data : "沒有文字可以預覽。");
                            } else {
                                alert("error");
                            }
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            alert(xhr.status);
                            alert(thrownError);
                        }
                    });
                }
            });
            $('#preview').css('min-height', $('#data').height() + 'px');
        @endif
    </script>
@endsection
