@extends('app')

@section('title')
    新增投票選項
@endsection

@section('content')
    <div class="container" style="min-height: 600px">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default">
                    <div class="panel-heading">新增投票選項</div>
                    {{-- Panel body --}}
                    <div class="panel-body">
                        <div class="row">
                            {!! Form::open(['route' => 'vote-selection.store', 'class' => 'form-horizontal']) !!}
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="creator">投票活動</label>
                                    <div class="col-md-9">
                                        {!! HTML::linkRoute('vote-event.show', $voteEvent->subject, $voteEvent->id, null) !!}
                                    </div>
                                </div>
                                <div class="form-group has-feedback{{ ($errors->has('title'))?' has-error':'' }}">
                                    <label class="control-label col-md-3" for="title">選項內容</label>
                                    <div class="col-md-9">
                                        {!! Form::text('title', null, ['id' => 'title', 'placeholder' => '請輸入選項內容', 'class' => 'form-control']) !!}
                                        @if($errors->has('title'))<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                                        <span class="label label-danger">{{ $errors->first('title') }}</span>@endif
                                    </div>
                                </div>
                                <div class="form-group has-feedback{{ ($errors->has('image'))?' has-error':'' }}">
                                    <label class="control-label col-md-3" for="image">圖片網址</label>
                                    <div class="col-md-9">
                                        {!! Form::textarea('image',  null, ['id' => 'image', 'placeholder' => '請輸入圖片連結，每行一個網址', 'class' => 'form-control', 'rows' => 5]) !!}
                                        @if($errors->has('image'))<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                                        <span class="label label-danger">{{ $errors->first('image') }}</span>@endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        {!! Form::file('image_upload', ['id' => 'image_upload', 'class' => 'form-control', 'accept' => 'image/*', 'multiple']) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-9 col-md-offset-3">
                                        {!! Form::hidden('vid', $voteEvent->id) !!}
                                        {!! Form::submit('新增投票選項', ['class' => 'btn btn-primary']) !!}
                                        {!! HTML::linkRoute('vote-event.show', '返回', $voteEvent->id, ['class' => 'btn btn-default']) !!}
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
    $("#image_upload").fileinput({
        'language': 'tw',
        'uploadUrl': '{{ URL::route('upload.image') }}',
        'deleteUrl': '{{ URL::route('upload.delete-image') }}',
        'multiple': true,
        'append': false,
        'uploadExtraData': {
            '_token': '{{ Session::token() }}'
        },
        'deleteExtraData': {
            '_token': '{{ Session::token() }}'
        },
        'overwriteInitial': false,
        'previewSettings': {
            image: {width: "200px", height: "auto"},
        }
    });
    $('#image_upload').on('fileuploaded', function(event, data, previewId, index) {
        var form = data.form, files = data.files, extra = data.extra,
        response = data.response, reader = data.reader;
        console.log('Uploaded: ' + response.url);
        $('textarea#image').val($.trim($('textarea#image').val() + '\n' + response.url));
    });
    $('#image_upload').on('filedeleted', function(event, key) {
        console.log('Deleted: ' + key);
        $('textarea#image').val($.trim($('textarea#image').val().replace(key, '').replace(/\n+/g, '\n')));
    });
@endsection
