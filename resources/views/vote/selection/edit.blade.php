@extends('app')

@section('title')
    編輯投票選項
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="well bs-component">
                    {!! Form::open(['route' => ['vote-selection.update', $voteSelection->id], 'class' => 'form-horizontal', 'method' => 'PUT']) !!}
                    <fieldset>
                        <legend>編輯投票選項</legend>
                        <div class="form-group">
                            <label class="col-md-2 control-label" for="creator">投票活動</label>

                            <div class="col-md-10 form-control-static">
                                {!! HTML::linkRoute('vote-event.show', $voteSelection->voteEvent->subject, $voteSelection->voteEvent->id, null) !!}
                            </div>
                        </div>
                        <div class="form-group has-feedback{{ ($errors->has('title'))?' has-error':'' }}">
                            <label class="col-md-2 control-label" for="title">選項內容</label>

                            <div class="col-md-10">
                                {!! Form::text('title', $voteSelection->getTitle(), ['id' => 'title', 'placeholder' => '請輸入選項內容', 'class' => 'form-control']) !!}
                                @if($errors->has('title'))<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                                <span class="label label-danger">{{ $errors->first('title') }}</span>@endif
                            </div>
                        </div>
                        <div class="form-group has-feedback{{ ($errors->has('image'))?' has-error':'' }}">
                            <label class="col-md-2 control-label" for="image">圖片網址</label>

                            <div class="col-md-10">
                                {!! Form::textarea('image',  $voteSelection->getImageLinkstext(), ['id' => 'image', 'placeholder' => '請輸入圖片連結，每行一個網址', 'class' => 'form-control', 'style' => 'resize: vertical;', 'rows' => 5]) !!}
                                @if($errors->has('image'))<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                                <span class="label label-danger">{{ $errors->first('image') }}</span>@endif
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12">
                                {!! Form::file('image_upload', ['id' => 'image_upload', 'class' => 'form-control', 'accept' => 'image/*', 'multiple']) !!}
                            </div>
                        </div>
                        <small>
                            <b>注意</b>：<br />
                            選擇圖片後，請按下「上傳」並等待上傳完成，網址將會自動填入上方欄位。<br />
                            圖片上傳平台為{!! link_to('http://imgur.com/', 'Imgur', ['target' => '_blank']) !!}，
                            上傳之圖片不得違反該站{!! link_to('http://imgur.com/tos', '服務條款', ['target' => '_blank', 'title' => 'Terms of Service - Imgur']) !!}。
                        </small>
                        <hr/>
                        <div class="form-group">
                            <div class="col-md-10 col-md-offset-1 text-center">
                                {!! Form::submit('修改資料', ['class' => 'btn btn-primary']) !!}
                                {!! HTML::linkRoute('vote-event.show', '返回', $voteSelection->voteEvent->id, ['class' => 'btn btn-default']) !!}
                            </div>
                        </div>
                    </fieldset>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script type="text/javascript">
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
            initialPreview: [
                @foreach($voteSelection->getImageLinks() as $image)
                    "<img src='{{ $image }}' class='file-preview-image' title='{{ substr($image, strrpos($image, '/') + 1) }}' style='max-width:200px;max-height:200px;width:auto;height:auto;'>",
                @endforeach
            ],
            initialPreviewConfig: [
                    @foreach($voteSelection->getImageLinks() as $key => $image)
                        {
                    caption: '{{ substr($image, strrpos($image, '/') + 1) }}',
                    url: '{{ URL::route('upload.delete-image') }}',
                    key: '{{ $image }}',
                },
                @endforeach
            ],
            'overwriteInitial': false,
            'previewSettings': {
                image: {width: "200px", height: "auto"},
            }
        });
        $('#image_upload').on('fileuploaded', function (event, data, previewId, index) {
            var form = data.form, files = data.files, extra = data.extra,
                response = data.response, reader = data.reader;
            console.log('Uploaded: ' + response.url);
            $('textarea#image').val($.trim($('textarea#image').val() + '\n' + response.url));
        });
        $('#image_upload').on('filedeleted', function (event, key) {
            console.log('Deleted: ' + key);
            $('textarea#image').val($.trim($('textarea#image').val().replace(key, '').replace(/\n+/g, '\n')));
        });
    </script>
@endsection
