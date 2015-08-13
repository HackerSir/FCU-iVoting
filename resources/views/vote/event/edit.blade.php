@extends('app')

@section('title')
    編輯投票活動
@endsection

@section('content')
    <div class="container" style="min-height: 600px">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="well bs-component">
                    {!! Form::open(['route' => ['vote-event.update', $voteEvent->id], 'class' => 'form-horizontal', 'method' => 'PUT']) !!}
                    <fieldset>
                        <legend>編輯投票活動</legend>
                    </fieldset>
                    <div class="form-group has-feedback{{ ($errors->has('subject'))?' has-error':'' }}">
                        <label class="control-label col-md-2" for="subject">投票主題</label>

                        <div class="col-md-9">
                            {!! Form::text('subject', $voteEvent->subject, ['id' => 'subject', 'placeholder' => '請輸入投票主題', 'class' => 'form-control', 'required']) !!}
                            @if($errors->has('subject'))<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                            <span class="label label-danger">{{ $errors->first('subject') }}</span>@endif
                        </div>
                    </div>
                    <div class="form-group has-feedback{{ ($errors->has('open_time'))?' has-error':'' }}">
                        <label class="control-label col-md-2" for="open_time">開始時間</label>

                        <div class="col-md-9">
                            @if(!$voteEvent->isStarted())
                                <div class='input-group date' id='datetimepicker1'>
                                    {!! Form::text('open_time', $voteEvent->open_time, ['id' => 'open_time', 'placeholder' => 'YYYY/MM/DD HH:mm:ss', 'class' => 'form-control']) !!}
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                            @else
                                {!! Form::text('open_time',$voteEvent->open_time, ['id' => 'open_time', 'placeholder' => 'YYYY/MM/DD HH:mm:ss', 'class' => 'form-control', 'readonly']) !!}
                            @endif
                            @if($errors->has('open_time'))<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                            <span class="label label-danger">{{ $errors->first('open_time') }}</span>@endif
                        </div>
                    </div>
                    <div class="form-group has-feedback{{ ($errors->has('close_time'))?' has-error':'' }}">
                        <label class="control-label col-md-2" for="close_time">結束時間</label>

                        <div class="col-md-9">
                            <div class='input-group date' id='datetimepicker2'>
                                {!! Form::text('close_time', $voteEvent->close_time, ['id' => 'close_time', 'placeholder' => 'YYYY/MM/DD HH:mm:ss', 'class' => 'form-control']) !!}
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                            @if($errors->has('close_time'))<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                            <span class="label label-danger">{{ $errors->first('close_time') }}</span>@endif
                        </div>
                    </div>
                    <div class="form-group has-feedback{{ ($errors->has('max_selected'))?' has-error':'' }}">
                        <label class="control-label col-md-2" for="max_selected">最多可選幾項</label>
                        <div class="col-md-9">
                            @if(!$voteEvent->isStarted())
                                {!! Form::number('max_selected', $voteEvent->max_selected, ['id' => 'max_selected', 'placeholder' => '每人最多可選擇之數量，預設為1', 'class' => 'form-control', 'min' => 1]) !!}
                                @if($errors->has('max_selected'))<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                                <span class="label label-danger">{{ $errors->first('max_selected') }}</span>@endif
                            @else
                                {!! Form::number('max_selected', $voteEvent->max_selected, ['id' => 'max_selected', 'placeholder' => '每人最多可選擇之數量，預設為1', 'class' => 'form-control', 'min' => 1, 'readonly']) !!}
                            @endif
                        </div>
                    </div>
                    <div class="form-group has-feedback{{ ($errors->has('organizer'))?' has-error':'' }}">
                        <label class="control-label col-md-2" for="organizer">主辦單位</label>
                        <div class="col-md-9">
                            @if(!$voteEvent->isStarted())
                                {!! Form::select('organizer', $organizerArray, $voteEvent->organizer_id, ['class' => 'form-control']) !!}
                                @if($errors->has('organizer'))<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                                <span class="label label-danger">{{ $errors->first('organizer') }}</span>@endif
                            @else
                                {!! Form::select('organizer', $organizerArray, $voteEvent->organizer_id, ['class' => 'form-control', 'disabled']) !!}
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2" for="settings">選項</label>

                        <div class="col-md-9">
                            @if($voteEvent->isStarted())
                                <div class="checkbox disabled">
                                    <label>
                                        {!! Form::checkbox('hideVoteEvent', 'true', !$voteEvent->show, ['disabled']) !!} 在開始前隱藏投票活動
                                    </label>
                                </div>
                            @else
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('hideVoteEvent', 'true', !$voteEvent->show) !!} 在開始前隱藏投票活動
                                    </label>
                                </div>
                            @endif

                            <div>
                                <label class="control-label" for="prefix" style="margin-bottom: 5px;">限制學號開頭
                                    <span class="glyphicon glyphicon-question-sign" title="請直接輸入學號開頭，如：d04；<br />若想同時允許多種學號，請用逗號分隔，如：d01,d02。"></span>
                                </label>

                                {!! Form::text('prefix', $voteEvent->getConditionValue('prefix'), ['id' => 'prefix', 'placeholder' => '只有特定學號開頭可投票，留白為不限制', 'class' => 'form-control']) !!}
                                @if($errors->has('prefix'))<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                                <span class="label label-danger">{{ $errors->first('prefix') }}</span>@endif
                            </div>
                            <div>
                                <label class="control-label" for="show_result" style="margin-bottom: 5px;">顯示投票結果</label>
                                {!! Form::select('show_result', ['always' => '總是顯示', 'after-vote' => '完成投票者可看見結果（活動結束後對所有人顯示）', 'after-event' => '活動結束後顯示'], $voteEvent->show_result, ['id' => 'show_result', 'class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group has-feedback{{ ($errors->has('info'))?' has-error':'' }}">
                        <label class="control-label col-md-2" for="info">內容簡介</label>

                        <div class="col-md-10" role="tabpanel">
                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active"><a href="#edit" aria-controls="edit" role="tab" data-toggle="tab" id="tab_edit">編輯</a></li>
                                <li role="presentation"><a href="#preview" aria-controls="preview" role="tab" data-toggle="tab" id="tab_preview">預覽</a></li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <!-- Tab panes -->
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="edit">
                                    {!! Form::textarea('info', $voteEvent->info, ['id' => 'info', 'placeholder' => '請輸入內容簡介', 'class' => 'form-control']) !!}
                                    <small>
                                        <b>提示：</b>內容簡介支援{!! link_to('http://markdown.tw/', 'Markdown', ['target' => '_blank']) !!}語法
                                    </small>
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
                        <div class="col-md-10 col-md-offset-1 text-center">
                            <hr/>
                            {!! Form::submit('修改資料', ['class' => 'btn btn-primary']) !!}
                            {!! HTML::linkRoute('vote-event.show', '返回', $voteEvent->id, ['class' => 'btn btn-default']) !!}
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
            if (e.target.id == 'tab_preview') {
                $("#preview").html("Loading...");

                var URLs = "{{ URL::route('markdown.preview') }}"
                var val = $('#edit textarea').val();

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
                            $("#preview").html(data);
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
        })
    </script>
@endsection
